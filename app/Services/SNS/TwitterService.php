<?php

namespace App\Services\SNS;

use App\Models\Admin;
use App\Models\LoginToken;
use App\Models\Media;
//use Laravel\Socialite\Contracts\User as TwitterUser;
use App\Models\User;
use App\Models\TwitterCredential;
use Twitter;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\File;
use App\Services\Image\ImageService;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;

class TwitterService
{
    private $imgService;

    public function __construct(ImageService $imageService)
    {
        $this->imgService = $imageService;
    }

    public function updateOrCreate($twUser, $jwt)
    {
        $account = TwitterCredential::where('provider_id', $twUser->provider_id)->first();

        if ($account) {
            $account->token = $twUser->token;
            $account->secret = $twUser->token_secret;
            $account->account_name = $twUser->account_name;
            $account->name = $twUser->name;
            $account->avatar = $twUser->avatar;
            $account->save();
        } else {
            $vcode = explode('|', $jwt);
            $loginToken = LoginToken::where([
                'access_token' => $vcode[0],
                'pin' => $vcode[1]
            ])->latest()->first();

            if(is_null($loginToken)) {
               return null;
            }
            $account = TwitterCredential::create([
                'user_id' => $loginToken->user_id,
                'token' => $twUser->token,
                'secret' => $twUser->token_secret,
                'provider_id' => $twUser->provider_id,
                'account_name' => $twUser->account_name,
                'name' => $twUser->name,
                'avatar' => $twUser->avatar
            ]);
        }
        return $account->user;
    }

    /**
     * post article message with photo
     * @param TwitterCredential $twCredential
     * @param string $tweetId
     * @return string
     * @throws
     */
    public function getSnsLink($twCredential, $tweetId)
    {
        $username = $twCredential->account_name === '' ? 'hello' : $twCredential->account_name;
        return "https://twitter.com/$username/status/$tweetId";
    }

    private function refreshTwitterCredential(TwitterCredential $twitterCredential)
    {
        $request_token = [
            'token' => $twitterCredential->token,
            'secret' => $twitterCredential->secret,
        ];

        Twitter::reconfig($request_token);
    }

    /**
     * upload video
     * @param string $url
     * @throws
    */
    public function uploadVideo($url)
    {
        $filename = basename($url);
        $t = explode('.mp4', $filename);
        $filename = $t[0] . '.mp4';
        $compressedName = public_path('tmp/'.$filename);
        //logger()->info($compressedName);
        if(!file_exists($compressedName)) {
            $t = str_replace('.', '', time().microtime(true));
            $fcont = file_get_contents($url);
            $tmpName = public_path('tmp/'.$t.'.mp4');
            file_put_contents($tmpName, $fcont);

            // compress
            try {
                $setting = array(
                    'ffmpeg.binaries'  => config('ffmpeg.binary.ffmpeg'),
                    'ffprobe.binaries' => config('ffmpeg.binary.ffprobe'),
                );
                $ffmpeg = \FFMpeg\FFMpeg::create($setting);
                $video = $ffmpeg->open($tmpName);

                $video->filters()
                    ->resize(new \FFMpeg\Coordinate\Dimension(640, 360))
                    ->synchronize();
                $format = new \FFMpeg\Format\Video\X264();
                $format->setVideoCodec('libx264');
                $format->setAudioCodec('aac');
//            $format->setAdditionalParameters( [ '-crf', '10'] );
                $video->save($format, $compressedName);
            } catch(\Exception $err) {
                //logger()->error($err->getMessage());
                throw new \Exception('video compression error');
            }
        }

        if(file_exists($compressedName)) {
            $newFileContents = file_get_contents($compressedName);
            $file_size = strlen($newFileContents);

            //logger()->info('file size= '.$file_size);

            // Initialize the media upload - no files are sent at this point
            // https://developer.twitter.com/en/docs/media/upload-media/api-reference/post-media-upload-init#parameters
            $init_media = Twitter::uploadMedia([
                'command' => 'INIT',
                'media_type' => 'video/mp4',
                'media_category' => 'tweet_video',
                'total_bytes' => $file_size,
            ]);
            //logger()->info($init_media->media_id_string);

            // Upload the first (or only) chunk
            // https://developer.twitter.com/en/docs/media/upload-media/uploading-media/chunked-media-upload
            $segment_id = 0;
            $bytes_sent = 0;
            $chunk_size = 4*1024*1024;

            while ($bytes_sent < $file_size) {
                $offset = $segment_id * $chunk_size;
                $chunk = substr($newFileContents, $offset, $chunk_size);

                Twitter::uploadMedia([
                    'media' => $chunk,
                    'command' => 'APPEND',
                    'segment_index' => strval($segment_id),
                    'media_id' => $init_media->media_id_string
                ]);
                $bytes_sent += strlen($chunk);

                $segment_id++;
            }
            //logger()->info('bytes sent '.$bytes_sent);

            // Finalize the video upload
            $final_media = Twitter::uploadMedia([
                'command' => 'FINALIZE',
                'media_id' => $init_media->media_id_string,
            ]);
            //logger()->info('finalize');

            // After a video has been uploaded it can take Twitter some time to process it before
            // it can be used in a Tweet. A better approach than the one below would be to use a
            // queue (e.g. Redis), but this demonstrates the logic.
            $waits = 0;

            while($waits <= 3) {
                // Check the status
                $status_media = Twitter::uploadStatus([
                    'command' => 'STATUS',
                    'media_id' => $init_media->media_id,
                ]);

                // Check that the STATUS command is returning a valid state
                if(isset($status_media->processing_info->state)) {
                    switch($status_media->processing_info->state) {
                        case 'succeeded':
                            $waits = 4; // break out of the while loop
                            break;
                        case 'failed':
                            throw new \Exception('Video processing failed: '.$status_media->processing_info->error->message);
                        default:
                            // Check how long Twitter tells us to wait for before checking the status again
                            $wait = $status_media->processing_info->check_after_secs;
                            sleep($wait);
                            $waits++;
                    }
                } else {
                    //logger()->error('unknown error');
                    throw new \Exception('There was an unknown error uploading your video');
                }
            }
        }
        return $final_media;
    }

    /** upload photo
     * @param string $url
     * @param string $club
     * @throws
    */
    public function uploadPhoto($url, $club) {
        $file = $this->imgService->getFileContentsWithCredit($url, $club);
        if(!$file) {
            throw new \Exception('File not exist');
        }

        $file_size = strlen($file);
        if($file_size > 5 * 1024 * 1024) {
            throw new \Exception('Image size over');
        }
        return Twitter::uploadMedia(['media' => $file]);
    }

    /** Verify media
    */

    /**
     * post article message with photo
     * @param TwitterCredential $twCredential
     * @param string $message
     * @param Media[] $medias
     * @param string[] $tags
     * @param string $club
     * @return array ['error' => 'error message', 'id' => 'article Id']
     * @throws
     */
    public function tweetMedia($twCredential, $message, $medias, $tags)
    {
        $this->refreshTwitterCredential($twCredential);
        try {
            if (count($tags) > 0) {
                $message .= "\n";
            }

            foreach ($tags as $key => $tag) {
                $message = $message . ($key === 0 ? '' : ' ') . '#' . $tag;
            }

            $newTweet = ['status' => $message];
            $mids = [];
            foreach ($medias as $media) {
                $url = $media->extension === 'mp4' ? $media->video_url : $media->source_url;
                if ($media->extension === 'mp4') {
                    //logger()->info('video upload');
                    $uploadedMedia = $this->uploadVideo($url);
                } else {
                    //logger()->info('photo upload');
                    $club = $media->creator;
//                    $club = $media->source === 'aws' ? '© J.LEAGUE' : '© '.Admin::find($media->club_id)->name_en;
                    $uploadedMedia = $this->uploadPhoto($url, $club);
                }
                if (!empty($uploadedMedia)) {
                    array_push($mids, $uploadedMedia->media_id_string);
                }
            }
            $newTweet['media_ids'] = implode(',', $mids);
            $response = Twitter::postTweet($newTweet);
            return ['error' => '', 'id' => $response->id];
        } catch (\Exception $e) {
            //logger()->error($e->getMessage());
            // wrong media format error
            if(strpos($e->getMessage(), 'video size over') !== false) {
                return ['error' => 'tw video size over', 'id' => ''];
            }
            if(strpos($e->getMessage(), 'Image size over') !== false) {
                return ['error' => 'tw photo size over', 'id' => ''];
            }
            if(strpos($e->getMessage(), '[324]') !== false) {
                return ['error' => 'tw media format error', 'id' => ''];
            }
            return ['error' => 'tw file upload error', 'id' => ''];
        }
    }

}
