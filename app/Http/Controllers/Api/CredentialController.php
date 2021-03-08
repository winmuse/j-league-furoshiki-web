<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\SNS\LineController;
use App\Http\Controllers\Controller;
use App\Models\FacebookCredential;
use App\Models\FacebookPage;
use App\Models\InstagramCredential;
use App\Models\LineCredential;
use App\Models\TwitterCredential;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CredentialController extends Controller
{
    public function __construct()
    {
    }

    /**
     * get user's SNS credential array
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        $userId = Auth::id();
        $user = User::find($userId);

        if (is_null($user)) {
            return response()->json([]);
        }

        $res = [];
        $credentials = $user->facebook_credentials;
        foreach ($credentials as $credential) {
            $pages = $credential->pages;
            foreach ($pages as $page) {
                $fb = [
                    'id' => $page->id,
                    'type' => 'facebook',
                    'account_name' => $page->name,
                    'name' => $credential->name,
                    'avatar' => $credential->avatar,
                    'active' => true,
                    'checkbox' => true
                ];
                array_push($res, $fb);
            }
        }
        $credentials = $user->twitter_credentials;
        foreach ($credentials as $credential) {
            $tw = [
                'id' => $credential->id,
                'type' => 'twitter',
                'account_name' => $credential->account_name,
                'name' => $credential->name,
                'avatar' => $credential->avatar,
                'active' => true,
                'checkbox' => true
            ];
            array_push($res, $tw);
        }
        $credentials = $user->instagram_credentials;
        foreach ($credentials as $credential) {
            $ig = [
                'id' => $credential->id,
                'type' => 'instagram',
                'account_name' => $credential->account_name,
                'name' => $credential->name,
                'avatar' => $credential->avatar,
                'active' => true,
                'checkbox' => true
            ];
            array_push($res, $ig);
        }
//        $credentials = $user->line_credentials;
//        foreach ($credentials as $credential) {
//            $ln = [
//                'id' => $credential->id,
//                'type' => 'line',
//                'account_name' => $credential->name,
//                'name' => $credential->name,
//                'avatar' => asset('/images/default-avatar.png'),
//                'active' => $credential->valid_flag === 1,
//                'checkbox' => true
//            ];
//        }
        return response()->json($res);
    }

    /**
     * remove user's SNS credential
     * @param string $sns
     * @param int $id
     * @return JsonResponse
     */
    public function remove($sns, $id): JsonResponse
    {
        try {
            switch ($sns) {
                case 'facebook':
                    $fbPage = FacebookPage::find($id);
                    $fbCredential = FacebookCredential::where([
                        'user_id' => $fbPage->user_id,
                        'provider_id' => $fbPage->provider_id
                    ])->first();
                    $fbPage->delete();
                    if ($fbCredential->pages->count() === 0) {
                        $fbCredential->delete();
                    }
                    break;
                case 'twitter':
                    TwitterCredential::find($id)->delete();
                    break;
                case 'instagram':
                    InstagramCredential::find($id)->delete();
                    break;
                case 'line':
                    LineCredential::find($id)->delete();
                    break;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['error' => '']);
    }

    /**
     * update password
     * @param Request $request (password, passowrd_confirm)
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
//      'email' => 'required|string|max:255|email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = Auth::id();
//    if ($user->email !== $request->email) {
//      return response()->json([
//        'email' => ['wrong email address']
//      ], 400);
//    }
        $user = User::find($userId);
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return response()->json([
            'error' => ''
        ], 200);
    }

    public function updatePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
//      'email' => 'required|string|max:255|email',
            'mobile' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = Auth::id();

//    if ($user->email !== $request->email) {
//      return response()->json([
//        'email' => ['wrong email address']
//      ], 400);
//    }
        $profile = User::find($userId)->profile;
        $profile->mobile = $request->input('mobile');
        $profile->save();
        return response()->json([
            'error' => ''
        ], 200);
    }
}
