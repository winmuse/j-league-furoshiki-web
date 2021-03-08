<?php

namespace App\Services\SNS;

use App\Models\LineCredential;
use App\Models\User;
use Illuminate\Http\Request;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;

class LineService
{
    public function updateOrCreate(Request $request, User $user)
    {
      try {
        /** @var LineCredential $account */
        $account = LineCredential::where('user_id', $user->id)->first();

        if (!$account) {
          $account = LineCredential::create([
            'user_id' => $user->id,
          ]);
        }

        $account->auth_url = $request->input('url');
//        $account->channel_secret = $request->input('channel_secret');
//        $account->access_token = $request->input('access_token');
        $account->save();
        return true;
      } catch (\Throwable $e) {
        //logger()->error($e->getMessage());
        return false;
      }
    }

    /**
     * post message and return response
     * @param User $user
     * @param string $message
     * @return LINEBot\Response
    */
    public function post($user, $message)
    {
        /** @var LineCredential $credential */
        $credential = LineCredential::whereUserId($user->id)->first();

        $httpClient = new CurlHTTPClient($credential->access_token);
        $bot = new LINEBot($httpClient, ['channelSecret' => $credential->channel_secret]);

        if (is_string($message)) {
            $message = new TextMessageBuilder($message);
        }

        return $bot->broadcast($message);
    }

    public function postWithPhoto($user, $data)
    {
        // 画像メッセージを作成
        $heroComponent = ImageComponentBuilder::builder()
            ->setUrl($data['image_url'])
            ->setSize(ComponentImageSize::FULL);

        $bodyTextComponent = [];

        $bodyTextComponent[] = TextComponentBuilder::builder()
            ->setText($data['text'])
            ->setWrap(true);

        $bodyComponent = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setContents($bodyTextComponent);

        $bubbleComponent = BubbleContainerBuilder::builder()
            ->setHero($heroComponent)
            ->setBody($bodyComponent);

        $builder = FlexMessageBuilder::builder()
            ->setAltText($data['text'])
            ->setContents($bubbleComponent);

        return $this->post($user, $builder);
    }

    public function postWithVideo($user, $data)
    {
        // 動画メッセージを作成
        $message = new VideoMessageBuilder($data['video_url'], $data['thumbnail_url']);

        return $this->post($user, $message);
    }
}
