<?php
namespace App\Http\Controllers\Api\SNS;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SNS\FacebookService as FBService;
use App\Services\SNS\InstagramService as IGService;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use App\Models\FacebookCredential;
use App\Models\FacebookPage;
use JWTAuth;

session_start();
class FacebookController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests, AuthenticatesUsers;

  protected $redirectTo = '/';
  private $fbService;
  private $igService;
  private $fb;

  public function __construct(
      Facebook $fb,
    FBService $fbService,
    IGService $igService
  )
  {
    $this->middleware(function ($request, $next) use ($fb, $fbService, $igService) {
      $this->fbService = $fbService;
      $this->igService = $igService;
      $this->fb = $fb;
      return $next($request);
    });
  }

  public function login(Request $request)
  {
    $token = $request->get('vcode');
    $request->session()->put('verifycode', $token);
    $request->session()->put('target', 'facebook');

    $helper = $this->fb->getRedirectLoginHelper();
    // $permission = ['manage_pages', 'publish_pages', 'pages_show_list', 'instagram_basic'];
    $permission = ['publish_to_groups', 'pages_show_list', 'pages_manage_posts', 'pages_read_engagement'];
    $callbackUrl = route('facebook.callback');
    $loginUrl = $helper->getLoginUrl($callbackUrl, $permission);

    return redirect()->to($loginUrl);
  }

  public function callback(Request $request)
  {
      $helper = $this->fb->getRedirectLoginHelper();

      try {
          $accessToken = $helper->getAccessToken();
      } catch(FacebookResponseException $e) {
	  logger($e->getMessage());
          return redirect()->to('/sns-error/facebook?code=token');
      } catch(FacebookSDKException $e) {
	  logger($e->getMessage());
          return redirect()->to('/sns-error/facebook?code=token');
      }

      if (! isset($accessToken)) {
          if ($helper->getError()) {
              return redirect()->to('/sns-error/facebook?code=cancel');
          } else {
              return redirect()->to('/sns-error/facebook?code=request');
          }
      }

      // Logged in
      $token = $accessToken->getValue();
      logger()->info('token: ' . $token);

      // The OAuth 2.0 client handler helps us manage access tokens
      $oAuth2Client = $this->fb->getOAuth2Client();

      if (! $accessToken->isLongLived()) {
          // Exchanges a short-lived access token for a long-lived one
          try {
              $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
          } catch (FacebookSDKException $e) {
              return redirect()->to('/sns-error/facebook?code=long_token');
          }
          //logger()->info('long lived token ' . $accessToken->getValue());
      }

      $fb_access_token = $accessToken->getValue();
      logger()->info('long live token: ' . $fb_access_token);

      // get user profile
      try {
          // Returns a `Facebook\FacebookResponse` object
          $response = $this->fb->get('/me?fields=id,name,picture.type(large)', $fb_access_token);
      } catch(FacebookResponseException $e) {
	logger($e->getMessage());
          return redirect()->to('/sns-error/facebook?code=profile');
      } catch(FacebookSDKException $e) {
	logger($e->getMessage());
          return redirect()->to('/sns-error/facebook?code=profile');
      }

      $user = $response->getGraphUser();

      $fb_user = [
          'token' => $fb_access_token,
          'name' => $user->getName(),
          'account_name' => '',
          'provider_id' => $user->getId(),
          'avatar' => $user->getPicture()->getUrl()
      ];

      $target = $request->session()->get('target');

      $jwt = $request->session()->get('verifycode');
      if(is_null($jwt)) {
          return redirect()->to('/sns-error/facebook?code=verifycode');
      }
      $request->session()->forget('target');
      if($target === 'facebook') {
          $user = $this->fbService->updateOrCreate((object)$fb_user, $jwt);
          if(is_null($user)) {
              return redirect()->to('/sns-error/facebook?code=verifycode');
          }
      } else {
          $error = $this->igService->updateOrCreate((object)$fb_user, $jwt);
          if($error !== '') {
              switch ($error) {
                  case 'no instagram page':
                      $code = 'page';
                      break;
                  case 'no profile':
                      $code = 'profile';
                      break;
                  case 'no login token':
                      $code = 'verfycode';
                      break;
              }
              return redirect()->to('/sns-error/instagram?code='.$code);
          }
      }

      return redirect('/conf-sns-set');
  }
  
    public function save(Request $request) {
    $fbUser = $request->all();

    $user = $this->fbService->updateOrCreateApp($fbUser);
    if(is_null($user)) {
        return response()->json(['FacebookPage' => '']);
    }
    return response()->json(['FacebookPage' => '']);
  }
}
