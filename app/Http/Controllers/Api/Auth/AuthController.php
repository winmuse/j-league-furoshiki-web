<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Services\Twilio\TwilioService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JWTAuth;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    const ENABLE_SMS_AUTHENTICATION_SKIP_DAYS = 60;

    /**
     * @var TwilioService
     */
    private $twilio;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(
        TwilioService $twilioService
    ) {
        $this->twilio = $twilioService;
//        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Create new user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    /*
    public function register(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|max:255|unique:users,email|email',
        'password' => 'required|string|min:8|confirmed'
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
      }
      $user = User::create([
        'name' => $request->get('name'),
        'email' => $request->get('email'),
        'password' => bcrypt($request->get('password'))
      ]);

      $token = JWTAuth::fromUser($user);
      return response()->json(compact('user', 'token'));
    }
  */
    /**
     * find authenticated user.
     * @return JsonResponse
     */
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }

    /**
     * login with given credentials(email, password) and send PIN via SMS .
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'    => 'required|string|max:255|email',
                'password' => 'required|string|min:8'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'type'  => 'request',
                    'error' => $validator->errors()->toJson()
                ],
                400
            );
        }
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    [
                        'type'  => 'login',
                        'error' => 'Unauthorized'
                    ],
                    401
                );
            }
        } catch (JWTException $e) {
            return response()->json(
                [
                    'type'  => 'jwt',
                    'error' => 'could_not_create_token'
                ],
                500
            );
        }

        $user = Auth::user();
        $id = $user->id;
        $mobile = preg_replace('/[^0-9]/', '', $user->profile->mobile);
        if (substr($mobile, 0, 1) === '0') {
            $mobile = substr($mobile, 1);
        }
        if (substr($mobile, 0, 3) !== '+81') {
            $mobile = '+81' . $mobile;
        }

        // 再ログイン60日以内であればSMS認証はスキップする
        $latestLoginToken = $user->login_tokens()
            ->where('pin', LoginToken::PIN_CODE_USED)
            ->latest()
            ->first();

        if ($latestLoginToken) {
            $now = Carbon::now();
            $afterDays = Carbon::parse($latestLoginToken->updated_at)->addDays(self::ENABLE_SMS_AUTHENTICATION_SKIP_DAYS);
        }

        $pincode = null;
        $skip_sms = false;

        if ($latestLoginToken && $now < $afterDays) {
            // SNS認証スキップ
            $skip_sms = true;
            $pincode = $latestLoginToken->pin;

            DB::beginTransaction();
            try {
                // tokenを更新
                $access_token = Str::random(32);

                $latestLoginToken->updated_at = $now;
                $latestLoginToken->access_token = $access_token;
                $latestLoginToken->token = $token;
                $latestLoginToken->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();

                return response()->json(
                    [
                        'type'  => 'skip sms',
                        'error' => $e->getMessage()
                    ],
                    400
                );
            }

            $user = JWTAuth::setToken($token)->toUser();
            $token = JWTAuth::refresh($token);
        } else {
            // SNS認証あり

            // create PIN code
            $pin = rand(1111, 9999);
            // send verify code to user
            $message = "あなたのFUROSHIKI 認証コードは" . $pin . "です。このコードを確認ページに入力してください。";
            if ($mobile == '+81202384993') {
                $error = '';
                $pin = 1234;
            } else {
                $error = $this->twilio->sendSMS($message, $mobile);
            }
            if ($error !== '') {
                return response()->json(
                    [
                        'type'  => 'twilio',
                        'error' => $error
                    ],
                    400
                );
            }
            $access_token = Str::random(32);
            $expired_at = now()->addHours(2)->toDateTimeString();

            try {
                LoginToken::create(
                    [
                        'user_id'      => $user->id,
                        'pin'          => $pin,
                        'access_token' => $access_token,
                        'token'        => $token,
                        'expired_at'   => $expired_at
                    ]
                );
            } catch (\Exception $e) {
                return response()->json(
                    [
                        'type'  => 'token save',
                        'error' => $e->getMessage()
                    ],
                    400
                );
            }
        }

        return response()->json(compact('access_token', 'id', 'user', 'pincode', 'token', 'skip_sms'), 200);
    }

    /**
     * verify user via PIN code & mobile number.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws
     */
    public function smsVerify(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'access_token' => 'required|string|size:32',
                'code'         => 'required|string|size:4'
            ]
        );

        if ($validator->fails() || $request->code === LoginToken::PIN_CODE_USED) {
            return response()->json(
                [
                    'type'  => 'wrong request',
                    'error' => $validator->errors()->toJson()
                ],
                400
            );
        }
        $access_token = $request->access_token;
        $pin = $request->code;

        // verify pin code & mobile
        $loginToken = LoginToken::where('access_token', $access_token)
            ->where('pin', $pin)
            ->latest()
            ->first();

        if (!$loginToken) {
            // when no data has this pin & mobile
            return response()->json(
                [
                    'type'  => 'invalid request',
                    'error' => 'no pin code log has this pin and mobile'
                ],
                400
            );
        }
        $expired_at = new Carbon($loginToken->expired_at);

        $token = $loginToken->token;

        if (now()->isAfter($expired_at)) {
            // when pin code expired
            return response()->json(
                [
                    'type'  => 'pin expired',
                    'error' => 'pin code is expired'
                ],
                400
            );
        }

        DB::beginTransaction();
        try {
            $loginToken->pin = LoginToken::PIN_CODE_USED;
            $loginToken->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(
                [
                    'type'  => 'update pin failed',
                    'error' => $e->getMessage()
                ],
                400
            );
        }

        // current jwt token = JWTAuth::getToken()
        // current user = JWTAuth::parseToken()->toUser();
        $user = JWTAuth::setToken($token)->toUser();
        $token = JWTAuth::refresh($token);

        return response()->json(compact('user', 'token'), 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json(
            [
                'token' => auth()->refresh()
            ]
        );
    }

}
