<?php

namespace App\Services\SNS;

use App\Models\Admin;
use App\Models\LoginToken;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphNode;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as FBUser;
use App\Models\User;
use App\Models\Media;
use App\Models\Tag;
use Facebook\Facebook;
use App\Models\FacebookPage;
use App\Models\FacebookCredential;
use App\Services\Image\ImageService;
use Tymon\JWTAuth\JWTAuth;

class FacebookService
{
    private $fb;
    private $imgService;

    public function __construct(
        Facebook $fb,
        ImageService $imageService
    )
    {
        $this->fb = $fb;
        $this->imgService = $imageService;
    }

    /**
     * Get Facebook Access token
     * @param Integer $userId
     * @return String accessToken
     */
    public function getAccessToken($userId)
    {
        $credential = FacebookCredential::where('user_id', $userId)->first();
        if ($credential) {
            return $credential->token;
        }
        return '';
    }

    /**
     * Get Profile of Facebook user
     * @param Facebook $fb
     * @param User $user
     * @return array ['error' => strirng, 'profile' => Object]
     * @throws
     */
    public function retrieveUserProfile($user)
    {
        $accessToken = $this->getAccessToken($user);
        if ($accessToken !== '') {
            try {
//                $params = "first_name,last_name,age_range,gender";
//                $response = $this->api->get('/me?fields='.$params);
                $response = $this->fb->get('/me', $accessToken);
            } catch (FacebookResponseException $e) {
                // When Graph returns an error
                return [
                    'error' => 'Graph returned an error: ' . $e->getMessage(),
                    'profile' => null
                ];
            } catch (FacebookSDKException $e) {
                // When validation fails or other local issues
                return [
                    'error' => 'Facebook SDK returned an error: ' . $e->getMessage(),
                    'profile' => null
                ];
            }
            $me = $response->getGraphUser();
            return [
                'error' => '',
                'profile' => $me->asJson()
            ];
        }
        return [
            'error' => 'not logged in',
            'profile' => null
        ];
    }

    public function getLongLiveToken($shortToken)
    {
        $config = config('services.facebook');

        try {
            $response = $this->fb->sendRequest(
                'GET',
                '/oauth/access_token',
                [
                    'client_id' => $config['client_id'],
                    'client_secret' => $config['client_secret'],
                    'grant_type' => 'fb_exchange_token',
                    'fb_exchange_token' => $shortToken
                ],
                $shortToken,
                null,
                'v5.0');
        } catch (FacebookResponseException $e) {
            return ['error' => $e->getMessage(), 'token' => ''];
        } catch (FacebookSDKException $e) {
            return ['error' => $e->getMessage(), 'token' => ''];
        }
        $graphNode = $response->getGraphNode();

        return [
            'error' => '',
            'token' => $graphNode['access_token']
        ];
    }

    public function updateOrCreate($fbUser, $jwt)
    {
        $account = FacebookCredential::where('provider_id', $fbUser->provider_id)->first();

        $vcode = explode('|', $jwt);
        $loginToken = LoginToken::where('access_token', $vcode[0])
            ->where('pin', $vcode[1])
            ->latest()
            ->first();
        if(is_null($loginToken)) {
            return null;
        }

        $userId = $loginToken->user_id;

        if ($account) {
            $account->name = $fbUser->name;
            $account->account_name = $fbUser->account_name;
            $account->token = $fbUser->token;
            $account->save();
        } else {
            $account = FacebookCredential::create([
                'user_id' => $userId,
                'provider_id' => $fbUser->provider_id,
                'token' => $fbUser->token,
                'account_name' => $fbUser->account_name,
                'name' => $fbUser->name,
                'avatar' => $fbUser->avatar
            ]);
        }

        // remove old page records
        FacebookPage::where('user_id', $userId)
            ->where('provider_id', $fbUser->provider_id)
            ->delete();

        // get and save facebook pages
        $res = $this->getUserPages($account);
        foreach ($res['pages'] as $page) {
            FacebookPage::create([
                 'user_id' => $userId,
                 'provider_id' => $fbUser->provider_id,
                 'page_id' => $page['id'],
                 'name' => $page['name'],
                 'category' => $page['category'],
                 'access_token' => $page['access_token']
             ]);
        }

        return $account->user;
    }
    public function updateOrCreateApp($fbUser)
    {
        $account = FacebookCredential::where('provider_id', $fbUser['provider_id'])->first();

        if ($account) {
            $account->name = $fbUser['name'];
            $account->account_name = $fbUser['account_name'];
            $account->token = $fbUser['token'];
            $account->save();
        } else {
            $account = FacebookCredential::create([
                'user_id' => $fbUser['user_id'],
                'provider_id' => $fbUser['provider_id'],
                'token' => $fbUser['token'],
                'account_name' => $fbUser['account_name'],
                'name' => $fbUser['name'],
                'avatar' => $fbUser['avatar']
            ]);
        }

        // remove old page records
        FacebookPage::where('user_id', $fbUser['user_id'])
            ->where('provider_id', $fbUser['provider_id'])
            ->delete();

        // get and save facebook pages
        $res = $this->getUserPages($account);
        foreach ($res['pages'] as $page) {
            FacebookPage::create([
                 'user_id' => $fbUser['user_id'],
                 'provider_id' => $fbUser['provider_id'],
                 'page_id' => $page['id'],
                 'name' => $page['name'],
                 'category' => $page['category'],
                 'access_token' => $page['access_token']
             ]);
        }

        return $account->user;
    }
    
    /**
     * upload file and return GraphNode
     * @param FacebookCredential $credential
     * @param String $pageId
     * @param String $url
     * @param String $club
     * @return
     * @throws
     */
    public function fileUpload($pgCredential, $url, $club, $video = false)
    {
        try {
            $pageId = $pgCredential->page_id;
            if ($video) {
                $response = $this->fb->post(
                    "/$pageId/videos",
                    [
//                        'source' => $this->fb->videoToUpload($tmpName),
//                        'published' => 'false'
                        'file_url' => $url
                    ],
                    $pgCredential->access_token
                );
            } else {
                $response = $this->fb->post(
                    "/$pageId/photos", // '/page-id/photos',
                    [
                        'url' => $this->imgService->getFileUrlWithCredit($url, $club),
                        'published' => 'false'
                    ],
                    $pgCredential->access_token
                );
            }
        } catch (FacebookResponseException $e) {
            //logger()->error($e->getMessage());
            return null;
        } catch (FacebookSDKException $e) {
            //logger()->error($e->getMessage());
            return null;
        }
        return $response->getGraphNode();
    }

    /**
     * find user's FB pages
     * @param FacebookCredential $credential
     * @return array ['pageAccessToken', 'error']
     * @throws
     */
    public function getUserPages($credential)
    {
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->fb->get(
                "/$credential->provider_id/accounts",
                $credential->token
            );
        } catch (FacebookResponseException $e) {
            return ['error' => $e->getMessage(), 'pages' => []];
        } catch (FacebookSDKException $e) {
            return ['error' => $e->getMessage(), 'pages' => []];
        }
        $resData = $response->getDecodedBody();

        return ['error' => '', 'pages' => $resData['data']];
    }

    /**
     * find url of post
     * @param string $postId
     * @return string
     * @throws
     */
    public function getSnsLink($postId)
    {
        return "https://www.facebook.com/$postId";
    }

    /**
     * find page access token of user's FB page
     * @param String $pageId
     * @param String $userAccessToken
     * @return array ['pageAccessToken', 'error']
     * @throws
     */
    public function getPageAccessToken($pageId, $userAccessToken)
    {
        try {
            // Returns a `FacebookFacebookResponse` object
            $response = $this->fb->get(
                "/$pageId",
                $userAccessToken
            );
        } catch (FacebookResponseException $e) {
            return ['error' => $e->getMessage(), 'pageAccessToken' => ''];
        } catch (FacebookSDKException $e) {
            return ['error' => $e->getMessage(), 'pageAccessToken' => ''];
        }
        $graphNode = $response->getGraphNode();

        return ['error' => '', 'pageAccessToken' => $graphNode['access_token']];
    }

    /**
     * post article message with photo
     * @param FacebookPage $credential
     * @param string $message
     * @param Media[] $medias
     * @param string[] $tags
     * @return array ['error' => 'error message', 'id' => 'article Id']
     * @throws
     */
    public function postMultiPhotos($credential, $message, $medias, $tags)
    {
        try {
            $ids = [];
            foreach ($medias as $media) {
                $club = $media->creator;
//                $club = $media->source === 'aws' ? '© J.LEAGUE' : '© '.Admin::find($media->club_id)->name_en;

                $isVideo = $media->extension === 'mp4';
                $url = $isVideo ? $media->video_url : $media->source_url;
                $res = $this->fileUpload($credential, $url, $club, $isVideo);
                if (is_null($res)) {
                    return [
                        'error' => 'fb media upload error',
                        'id' => ''
                    ];
                }
                array_push($ids, ['media_fbid' => $res['id']]);
            }

            array_unshift($tags, $message);
            $message = implode(' #', $tags);

            $postData = [
                'message' => $message,
                'attached_media' => $ids,
            ];

            $response = $this->fb->post(
                "/$credential->page_id/feed",
                $postData,
                $credential->access_token
            );
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            //logger()->error($e->getMessage());
            return [
                'error' => 'Graph error',
                'id' => ''
            ];
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            //logger()->error($e->getMessage());
            return [
                'error' => 'Facebook SDK error',
                'id' => ''
            ];
        }
        $graphNode = $response->getGraphNode();
        return [
            'error' => '',
            'id' => $graphNode['id']
        ];
    }

    /**
     * post article message with photo
     * @param FacebookPage $credential
     * @param string $message
     * @param Media $video
     * @param string[] $tags
     * @return array ['error' => 'error message', 'id' => 'article Id']
     * @throws
     */
    public function postVideo($credential, $message, $video, $tags)
    {
        try {

            array_unshift($tags, $message);
            $message = implode(' #', $tags);

            $postData = [
                'description' => $message,
                'file_url' => $video->video_url,
            ];

            $response = $this->fb->post(
                "/$credential->page_id/videos",
                $postData,
                $credential->access_token
            );
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            //logger()->error($e->getMessage());
            return [
                'error' => 'Graph error',
                'id' => ''
            ];
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            //logger()->error($e->getMessage());
            return [
                'error' => 'Facebook SDK error',
                'id' => ''
            ];
        }
        $graphNode = $response->getGraphNode();
        return [
            'error' => '',
            'id' => $graphNode['id']
        ];
    }
}
