<?php

namespace App\Http\Controllers\Api\SNS;

use App\Services\SNS\FacebookService as FBService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\InstagramCredential;
use App\Services\SNS\InstagramService as IGService;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Facebook\Facebook;

use JWTAuth;

class InstagramController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests, AuthenticatesUsers;

  protected $redirectTo = '/';
  private $api;

  public function __construct(
    FBService $fbService
  )
  {
    $this->middleware(function ($request, $next) use ($fbService) {
      $this->api = $fbService;
      return $next($request);
    });
  }

  public function login(Request $request)
  {
    $token = $request->get('vcode');
    $request->session()->put('verifycode', $token);
    $request->session()->put('target', 'instagram');
    return Socialite::driver('facebook')
      ->stateless()
      ->scopes([
        'manage_pages',
        'publish_pages',
        'pages_show_list',
        'instagram_basic',
//        'instagram_content_publish'
      ])
      ->redirect();
  }

  public function callback(Request $request)
  {
    try {
      $fb_user = Socialite::driver('facebook')->user();
    } catch (InvalidStateException  $e) {
      return redirect('/sns/login/instagram');
    }

    $jwt = $request->session()->get('verifycode');
    $user = $this->api->updateOrCreate($fb_user, $jwt);
    $request->session()->forget('target');
    return redirect()->to('/conf-sns-set'); // Redirect to a secure page
  }

  public function get() {
    $userId = Auth::id();
    $ig = InstagramCredential::where('user_id', $userId)->first();

    return response()->json(['ig_credential' => $ig]);
  }

  public function save(Request $request) {
    $input = $request->all();

    $validator = Validator::make($input, [
      'email' => 'required',
      'password' => 'required'
    ]);
    if($validator->fails()) {
      return response()->json(['error' => 'wrong request'], 400);
    }
    $userId = Auth::id();
    $credential = InstagramCredential::where('user_id', $userId)->first();
    if(is_null($credential)) {
      InstagramCredential::create([
        'user_id' => $userId,
        'account_name' => $input['email'],
        'ig_password' => $input['password']
      ]);
    } else {
      $credential->account_name = $input['email'];
      $credential->ig_password = $input['password'];

      $credential->save();
    }
    return response()->json(['error' => '']);
  }
}
