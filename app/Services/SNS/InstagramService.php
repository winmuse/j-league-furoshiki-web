<?php

namespace App\Services\SNS;

use App\Models\Admin;
use App\Models\LoginToken;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphNode;
use Laravel\Socialite\Contracts\User as FBUser;
use App\Models\User;
use App\Models\Media;
use App\Models\InstagramCredential;
use App\Services\Image\ImageService;

class InstagramService
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

    private function instagram_id_to_url($instagram_id){
        $url_prefix = "https://www.instagram.com/p/";
        if(!empty(strpos($instagram_id, '_'))){
            $parts = explode('_', $instagram_id);
            $instagram_id = $parts[0];
        }

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
        $url_suffix = '';

        while($instagram_id > 0){
            $remainder = $instagram_id % 64;
            $instagram_id = ($instagram_id-$remainder) / 64;
            $url_suffix = $alphabet{$remainder} . $url_suffix;
        };

        return $url_prefix.$url_suffix;
    }

    /**
     * find url of post
     * @param string $postId
     * @return string
     * @throws
     */
    public function getSnsLink($postId)
    {
        return $this->instagram_id_to_url($postId);
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
                'v3.3');
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
        // get pages to find Instagram connected page
        $token = $fbUser->token;
        $res = $this->fb->get('/me/accounts', $token);
        $decoded = $res->getDecodedBody();

        $instagramPageId = null;
        $instagramBusinessAccount = null;
        foreach ($decoded['data'] as $pg) {
            $url = '/' . $pg['id'] . '/?fields=instagram_business_account';
            $res = $this->fb->get($url, $token);
            $data = $res->getDecodedBody();
            if (!array_key_exists('instagram_business_account', $data)) continue;
            $instagramPageId = $pg['id'];
            $instagramBusinessAccount = $data['instagram_business_account']['id'];
            break;
        }

        if (is_null($instagramPageId) || is_null($instagramBusinessAccount)) {
            return 'no instagram page';
        }

        // get instagram account information
        $url = "/$instagramBusinessAccount?fields=name,ig_id,username,profile_picture_url";
        try {
            $res = $this->fb->get($url, $token);
        } catch (FacebookResponseException $e) {
            return 'no profile';
        } catch (FacebookSDKException $e) {
            return 'no profile';
        }
        $igAccount = $res->getDecodedBody();
        $userName = $igAccount['username'];
        $name = array_key_exists('name', $igAccount)? $igAccount['name'] : '';
        $ig_id = array_key_exists('ig_id', $igAccount)? $igAccount['ig_id'] : '';
        $avatar = array_key_exists('profile_picture_url', $igAccount)? $igAccount['profile_picture_url'] : '';

        $account = InstagramCredential::where('fb_id', $fbUser->provider_id)->first();

        if ($account) {
            $account->token = $token;
            $account->page_id = $instagramPageId;
            $account->fb_id = $fbUser->provider_id;
            $account->account_name = $userName;
            $account->name = $name;
            $account->ig_user_id = $ig_id;
            $account->ig_business_id = $instagramBusinessAccount;
            $account->avatar = $avatar;
            $account->save();
        } else {
            $vcode = explode('|', $jwt);
            $loginToken = LoginToken::where([
                    'access_token' => $vcode[0],
                    'pin' => $vcode[1]
                ])->latest()->first();
            if(is_null($loginToken)) {
                return 'no login token';
            }
            $userId = $loginToken->user_id;

            $account = InstagramCredential::create([
                'user_id' => $userId,
                'page_id' => $instagramPageId,
                'token' => $token,
                'fb_id' => $fbUser->provider_id,
                'account_name' => $userName,
                'name' => $name,
                'ig_user_id' => $ig_id,
                'ig_business_id' => $instagramBusinessAccount,
                'avatar' => $avatar
            ]);
        }
        return '';
    }

    /**
     * return access token
     * @param int $userId
     * @return String
     */
    private function getAccessToken($userId)
    {
        $credential = User::find($userId)->instagram_credential;
        return $credential->token;
    }

    /**
     * return user's Facebook profile
     * @param User $user
     * @return array ['error' => string, 'profile' => GraphNode]
     */
    public function retrieveUserProfile(User $user)
    {
        $accessToken = $user->instagram_credential->token;
        if ($accessToken != '') {
            try {
                $response = Facebook::get('/me/accounts', $accessToken);
            } catch (FacebookResponseException $e) {
                //logger()->error($e->getMessage());
                return [
                    'error' => 'Graph error',
                    'profile' => null
                ];
            } catch (FacebookSDKException $e) {
                //logger()->error($e->getMessage());
                return [
                    'error' => 'Facebook SDK error',
                    'profile' => null
                ];
            }

            return [
                'error' => '',
                'profile' => $response->getDecodedBody()
            ];
        }
        return [
            'error' => 'no token',
            'profile' => null
        ];
    }

    /**
     * publish media container
     * @param InstagramCredential $credential
     * @param String $containerId
     * @return array ['error' => string, 'id' => articleId]
     * @throws
     */
    public function publishMediaContainer($credential, $containerId)
    {
        // publish container
        $uri = sprintf(
            '/%s/media_publish?creation_id=%s',
            $credential->ig_business_id, $containerId
        );

        try {
            $response = $this->fb->post($uri, null, $credential->token);
        } catch (FacebookResponseException $e) {
            //logger()->error($e->getMessage());
            return [
                'error' => 'Graph error',
                'id' => ''
            ];
        } catch (FacebookSDKException $e) {
            //logger()->error($e->getMessage());
            return [
                'error' => 'Facebook error',
                'id' => ''
            ];
        }

        return [
            'error' => '',
            'id' => $response->getGraphNode()->asJson()->id
        ];
    }

    /**
     * create media container
     * @param InstagramCredential $igCredential
     * @param String $message
     * @param Media $media
     * @return array['error' => error , 'id'=>containerId]
     * @throws
     */
    public function createMediaContainer($igCredential, $message, $media)
    {
        if (is_null($igCredential))
            return ['error' => 'no credential', 'id' => ''];

        $club = $media->source === 'aws' ? '© J.LEAGUE' : '© '.Admin::find($media->club_id)->name_en;
        // create photo container
        if ($media->extension === 'mp4') {
            $uri = sprintf(
                '/%s/media?media_type=video&video_url=%s&caption=%s',
                $igCredential->ig_business_id, $media->video_url, $message
            );
        } else {
            $photoUrl = $this->imgService->getFileUrlWithCredit($media->source_url, $club);
            $uri = sprintf(
                '/%s/media?image_url=%s&caption=%s',
                $igCredential->ig_business_id,
                $photoUrl,
                $message
            );
        }
        try {
            $response = $this->fb->post($uri, [], $igCredential->token);
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            //logger()->error($e->getMessage());
            return [
                'error' => 'Graph error',
                'id' => ''
            ];
        } catch (FacebookSDKException $e) {
            //logger()->error($e->getMessage());
            return [
                'error' => 'Facebook SDK error',
                'id' => ''
            ];
        }
        return [
            'error' => '',
            'id' => $response->getGraphNode()->asJson()->id
        ];
    }

    /**
     * post media to Instagram
     * @param InstagramCredential $igCredential
     * @param String $message
     * @param Media $media
     * @return array['error' => error , 'id'=>containerId]
     * @throws
     */
    public function postMedia($igCredential, $message, $media)
    {
        if (is_null($igCredential))
            return ['error' => 'no credential', 'id' => ''];

        // create photo container
        $response = $this->createMediaContainer($igCredential, $message, $media);

        if ($response['error'] !== '') return $response;

        return $this->publishMediaContainer($igCredential, $response['id']);
    }

}
