<?php

namespace App\Http\Controllers\Api\SNS;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\TwitterCredential;
use Twitter;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use App\Services\SNS\TwitterService;
use JWTAuth;

class TwitterController extends BaseController
{
  private $api;

  public function __construct(TwitterService $twitterService)
  {
    $this->middleware(function ($request, $next) use ($twitterService) {
      $this->api = $twitterService;
      return $next($request);
    });

//    Twitter::reconfig(['token' => '', 'secret' => '']);
  }

  public function login(Request $request)
  {
    $token = $request->get('vcode');
    $request->session()->put('verifycode', $token);

      $sign_in_twitter = true;
      $force_login = true;

      // Make sure we make this request w/o tokens, overwrite the default values in case of login.
      Twitter::reconfig(['token' => '', 'secret' => '']);
      $token = Twitter::getRequestToken(route('twitter.callback'));

      if (isset($token['oauth_token_secret']))
      {
          $url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);
          Session::put('tw_oauth_state', 'start');
          Session::put('tw_oauth_request_token', $token['oauth_token']);
          Session::put('tw_oauth_request_token_secret', $token['oauth_token_secret']);

          return redirect()->to($url);
      }

      return redirect()->to('/sns-error/twitter?code=token');
  }

  public function callback(Request $request)
  {
      if (Session::has('tw_oauth_request_token'))
      {
          $request_token = [
              'token'  => Session::get('tw_oauth_request_token'),
              'secret' => Session::get('tw_oauth_request_token_secret'),
          ];

          Twitter::reconfig($request_token);
          if ($request->has('oauth_verifier')) {
              $oauth_verifier = $request->get('oauth_verifier');
              $token = Twitter::getAccessToken($oauth_verifier);
          }

          if (!isset($token['oauth_token_secret'])) {
              return redirect('/sns-error/twitter?code=cancel');
          }

          $credentials = Twitter::getCredentials();

          if (is_object($credentials) && !isset($credentials->error)) {
              $jwt = $request->session()->get('verifycode', null);
              if(is_null($jwt)) {
                  return redirect('/sns-error/twitter?code=verifycode');
              }
              $twUser = [
                  'token' => $token['oauth_token'],
                  'token_secret' => $token['oauth_token_secret'],
                  'provider_id' => isset($credentials->id_str)? $credentials->id_str: '',
                  'account_name' => isset($credentials->screen_name)? $credentials->screen_name: '',
                  'name' => isset($credentials->name) ? $credentials->name : '',
                  'avatar' => isset($credentials->profile_image_url_https)? $credentials->profile_image_url_https : '',
              ];

              $user = $this->api->updateOrCreate((object)$twUser, $jwt);

              if(is_null($user)) {
                  return redirect('/sns-error/twitter?code=verifycode');
              }

              return redirect()->to('/conf-sns-set');
          }

          return redirect()->to('/sns-error/twitter?code=credential');
      }

      return redirect()->to('/sns-error/twitter?code=session');
  }
  
  public function save(Request $request) {
    $twUser = $request->all();
    $credential = TwitterCredential::where('provider_id', $twUser['provider_id'])->first();
    if(is_null($credential)) {
        $credential = TwitterCredential::create([
            'user_id' => $twUser['user_id'],
            'token' => $twUser['authToken'],
            'secret' => $twUser['authTokenSecret'],
            'provider_id' => $twUser['provider_id'],
            'account_name' => $twUser['account_name'],
            'name' => $twUser['name'],
            'avatar' => "a"
        ]);
    } else {
        $credential->token = $twUser['authToken'];
        $credential->secret = $twUser['authTokenSecret'];
        $credential->account_name = $twUser['account_name'];
        $credential->name = $twUser['name'];
        $credential->save();
    }
    return response()->json(['TwitterCredential' => '']);
  }
}
