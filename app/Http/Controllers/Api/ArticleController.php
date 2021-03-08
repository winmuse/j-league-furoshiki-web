<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Article;
use App\Models\ArticleProvider;
use App\Models\FacebookPage;
use App\Models\InstagramCredential;
use App\Models\Media;
use App\Models\Provider;
use App\Models\SnsUrl;
use App\Models\Tag;
use App\Models\TwitterCredential;
use App\Models\User;
use App\Services\Article\ArticleInstagramService;
use App\Services\SNS\FacebookService;

//use App\Services\SNS\InstagramService;
use App\Services\SNS\TwitterService;
use App\Services\SNS\LineService;
use App\Services\Media\MediaUsageService;
use App\Services\Article\ArticleService;
use App\Services\Image\ImageService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\JWTAuth;

class ArticleController extends Controller
{
    private $facebookService;
    private $twitterService;
//    private $instagramService;
    private $instagramService;
    private $lineService;
    private $mediaUsageService;
    private $articleService;
    private $articleInstagramService;
    private $imageService;

    public function __construct(
        FacebookService $fbService,
//        InstagramService $igService,
        TwitterService $twService,
        MediaUsageService $mdService,
        ArticleService $atService,
        ArticleInstagramService $atIgService,
        LineService $lnService,
        ImageService $imageService
    ) {
        $this->facebookService = $fbService;
        $this->twitterService = $twService;
        $this->mediaUsageService = $mdService;
        $this->articleService = $atService;
        $this->articleInstagramService = $atIgService;
        $this->lineService = $lnService;
        $this->imageService = $imageService;
    }

    /**
     * 同じ試合の動画が投稿済みか判定する
     * @param $mid media id
     * @return JsonResponse
     */
    public function checkVideoPosted($mid): JsonResponse
    {
        // get club and created date of media
        $media = Media::find($mid);
        if (is_null($media)) {
            return response()->json(['count' => -1]);
        }

        // 試合名のみだと過去の試合と重複の恐れがあるため、
        //「撮影日」+「イベント名」+「試合名」+「撮影場所」で判定する。
        $gameName = $media->meta->game_date . '_'
            . $media->meta->event . '_'
            . $media->meta->game . '_'
            . $media->meta->game_date;

        // get current user id
        $userId = Auth::id();
        $articles = User::find($userId)->articles;
        $count = 0;

        foreach ($articles as $article) {
            foreach ($article->medias as $postedMedia) {
                $postedGameName = $postedMedia->meta->game_date . '_'
                    . $postedMedia->meta->event . '_'
                    . $postedMedia->meta->game . '_'
                    . $postedMedia->meta->game_date;

                if ($postedGameName === $gameName && $postedMedia->extension === 'mp4') {
                    $count++;
                }
            }
        }
        return response()->json(['count' => $count]);
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function list()
    {
        $userId = Auth::id();
        $publics = $this->articleService->getPublics($userId);
        $privates = $this->articleService->getDrafts($userId);

        return response()->json(compact('publics', 'privates'));
    }

    /**
     * Display a listing of the resource.
     * @param int $id
     * @return JsonResponse
     */
    public function get($id)
    {
        $post = $this->articleService->getArticle($id);

        if (!$post) {
            return response()->json(['error' => 'no post'], 400);
        }

        return response()->json($post);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $userId = Auth::id();
        $content = $request->input('content');
        $mediaIds = $request->input('medias');
        $tags = $request->input('tags');
        $targets = $request->input('targets');
        $publish_at = $request->input('publish');
        $status = intval($request->input('status'));
        $comments = $request->input('comments', []);

        $medias = Media::whereIn('id', $mediaIds)->get();

        $adminId = User::find($userId)->profile->admin_id;

        // post article
        $snsLinks = array(
            'facebook'  => '',
            'twitter'   => '',
            'instagram' => '',
            'line'      => '',
        );
        if ($status === 1) {
            foreach ($targets as $target) {
                $provider = Provider::where('name', $target)->first();
                switch ($target['sns']) {
                    case 'facebook':
                        $fbPage = FacebookPage::find($target['id']);
                        if (count($medias) > 1) {
                            $result = $this->facebookService->postMultiPhotos($fbPage, $content, $medias, $tags);
                        } else {
                            if ($medias[0]->extension === 'mp4') {
                                $result = $this->facebookService->postVideo($fbPage, $content, $medias[0], $tags);
                            } else {
                                $result = $this->facebookService->postMultiPhotos($fbPage, $content, $medias, $tags);
                            }
                        }
                        if ($result['error'] == '') {
                            $snsLinks['facebook'] = $this->facebookService->getSnsLink($result['id']);
                        }
                        break;
                    case 'twitter':
                        $twCredential = TwitterCredential::find($target['id']);
                        $contentWithComment = $content . ' ' . implode(' ', $comments);
                        $result = $this->twitterService->tweetMedia($twCredential, $contentWithComment, $medias, $tags);
                        if ($result['error'] == '') {
                            $snsLinks['twitter'] = $this->twitterService->getSnsLink($twCredential, $result['id']);
                        }
                        break;
                    case 'instagram':
                        $media = $medias[0];
                        if ($media->extension === 'mp4') {
                            $filename = basename($media->video_url);
                            $tmpname = public_path('tmp/' . $filename);
                            if (!file_exists($tmpname)) {
                                $file = file_get_contents($media->video_url);
                                file_put_contents($tmpname, $file);
                            }
                            $url = url('/tmp/' . $filename);
                        } else {
                            $club = $media->creator;
                            $url = $this->imageService->getFileUrlWithCredit($media->source_url, $club);
                        }
                        $snsLinks['instagram'] = $url;
                        $result = ['error' => '', 'id' => ''];
                        break;
                    case 'line':
                        $user = User::find($userId);
                        if (count($tags) > 0) {
                            $content .= ' #' . implode(' #', $tags);
                        }
                        if ($medias[0]->extension === 'mp4') {
                            $response = $this->lineService->postWithVideo(
                                $user,
                                [
                                    'video_url'     => $medias[0]->video_url,
                                    'thumbnail_url' => $medias[0]->thumb_url,
                                ]
                            );
                            if ($response->isSucceeded()) {
                                $response = $this->lineService->post($user, $content);
                                $result = ['error' => $response->isSucceeded() ? '' : $response->getRawBody()];
                            } else {
                                $result = ['error' => ''];
                            }
                        } else {
                            $response = $this->lineService->postWithPhoto(
                                $user,
                                [
                                    'text'      => $content,
                                    'image_url' => $medias[0]->source_url,
                                ]
                            );
                            $result = ['error' => $response->isSucceeded() ? '' : $response->getRawBody()];
                        }
                        $snsLinks['line'] = '';
                        break;
                }
                if ($result['error'] !== '') {
                    return response()->json(['error' => $result['error']], 400);
                }
            }
        }

        DB::beginTransaction();

        try {
            $article = $this->articleService->newArticle($userId, $content, $publish_at, $status, $tags);
            // Instagram
            foreach ($targets as $target) {
                if ($target['sns'] === 'instagram') {
                    $igCredential = InstagramCredential::find($target['id']);
                    if (is_null($igCredential) || is_null($igCredential->account_name) || is_null(
                            $igCredential->ig_password
                        )) {
                        $result['error'] = 'no IG credential';
                        $snsLinks['instagram'] = '';
                    } else {
                        $this->articleInstagramService->newArticleInstagram($article->id, $snsLinks['instagram']);
                    }
                }
            }

            // register article_provider
            ArticleProvider::where('article_id', $article->id)->delete();
            SnsUrl::where('article_id', $article->id)->delete();

            foreach ($targets as $target) {
                $sns = $target['sns'];
                $tid = $target['id'];
                $this->articleService->saveArticleCredentialRelation($article->id, $sns, $tid);
                $this->articleService->saveArticleUrl($article->id, $sns, $tid, $snsLinks[$sns]);
            }

            // register article_media
            foreach ($medias as $media) {
                $this->articleService->addArticleMediaRelation($article->id, $media->id);
            }

            // save tags
            foreach ($tags as $t) {
                Tag::create(['article_id' => $article->id, 'name' => $t]);
            }
        } catch (\Throwable $e) {
            //logger()->error($e->getMessage());
            DB::rollBack();
            return response()->json(
                [
                    'error' => 'db error'
                ],
                500
            );
        }
        DB::commit();

        return response()->json(['error' => ''], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $id = intval($request->input('id'));
        $content = $request->input('content');
        $tags = $request->input('tags');
        $targets = $request->input('targets');
        $comments = $request->input('comments', []);
        $mediaIds = $request->input('medias');
        $status = $request->input('status');

        $medias = Media::whereIn('id', $mediaIds)->get();

        $userId = Auth::id();

        // post article
        $snsLinks = array(
            'facebook'  => '',
            'twitter'   => '',
            'instagram' => '',
            'line'      => '',
        );

        if ($status === 1) {
            foreach ($targets as $target) {
                switch ($target['sns']) {
                    case 'facebook':
                        $fbPage = FacebookPage::find($target['id']);
                        $result = $this->facebookService->postMultiPhotos($fbPage, $content, $medias, $tags);
                        if ($result['error'] == '') {
                            $snsLinks['facebook'] = $this->facebookService->getSnsLink($result['id']);
                        }
                        break;
                    case 'twitter':
                        $twCredential = TwitterCredential::find($target['id']);
                        $contentWithComment = $content . ' ' . implode(' ', $comments);
                        $result = $this->twitterService->tweetMedia($twCredential, $contentWithComment, $medias, $tags);
                        if ($result['error'] == '') {
                            $snsLinks['twitter'] = $this->twitterService->getSnsLink($twCredential, $result['id']);
                        }
                        break;
                    case 'instagram':
                        $media = $medias[0];
                        if ($media->extension === 'mp4') {
                            $filename = basename($media->video_url);
                            $tmpname = public_path('tmp/' . $filename);
                            if (!file_exists($tmpname)) {
                                $file = file_get_contents($media->video_url);
                                file_put_contents($tmpname, $file);
                            }
                            $url = url('/tmp/' . $filename);
                        } else {
                            $club = $media->creator;
                            $url = $this->imageService->getFileUrlWithCredit($media->source_url, $club);
                        }
                        $snsLinks['instagram'] = $url;
                        $result = ['error' => '', 'id' => ''];
                        break;
                    case 'line':
                        $user = User::find($userId);
                        if (count($tags) > 0) {
                            $content .= ' #' . implode(' #', $tags);
                        }
                        if ($medias[0]->extension === 'mp4') {
                            $response = $this->lineService->postWithVideo(
                                $user,
                                [
                                    'video_url'     => $medias[0]->video_url,
                                    'thumbnail_url' => $medias[0]->thumb_url,
                                ]
                            );
                            if ($response->isSucceeded()) {
                                $response = $this->lineService->post($user, $content);
                                $result = ['error' => $response->isSucceeded() ? '' : $response->getRawBody()];
                            } else {
                                $result = ['error' => ''];
                            }
                        } else {
                            $response = $this->lineService->postWithPhoto(
                                $user,
                                [
                                    'text'      => $content,
                                    'image_url' => $medias[0]->source_url,
                                ]
                            );
                            $result = ['error' => $response->isSucceeded() ? '' : $response->getRawBody()];
                        }
                        $snsLinks['line'] = '';
                        break;
                }
                if ($result['error'] !== '') {
                    return response()->json(['error' => $result['error']], 400);
                }
            }
        }

        DB::beginTransaction();

        try {
            $article = Article::find($id);
            $article->description = $content;
            $article->status = $status;
            $article->publish_at = now();
            $article->save();

            // Instagram
            foreach ($targets as $target) {
                switch ($target['sns']) {
                    case 'instagram':
                        $igCredential = User::find($userId)->instagram_credential;
                        if (is_null($igCredential) || is_null($igCredential->ig_meail) || is_null(
                                $igCredential->ig_password
                            )) {
                            $result['error'] = 'no IG credential';
                            $snsLinks['instagram'] = '';
                        } else {
                            $this->articleInstagramService->newArticleInstagram($article->id, $snsLinks['instagram']);
                        }
                        break;
                }
            }

            // save tags
            Tag::where('article_id', $id)->delete();
            foreach ($tags as $t) {
                Tag::create(['article_id' => $id, 'name' => $t]);
            }

            // register article_provider
            ArticleProvider::where('article_id', $id)->delete();
            SnsUrl::where('article_id', $id)->delete();
            foreach ($targets as $target) {
                $sns = $target['sns'];
                $tid = $target['id'];
                $this->articleService->saveArticleCredentialRelation($id, $sns, $tid);
                $this->articleService->saveArticleUrl($id, $sns, $tid, $snsLinks[$sns]);
            }
        } catch (QueryException $e) {
            DB::rollBack();
            //logger()->error($e->getMessage());
            return response()->json(
                [
                    'error' => 'db error'
                ],
                500
            );
        }
        DB::commit();

        return response()->json(['error' => ''], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        DB::table('articles')->delete($id);
        return response()->json(['error' => ''], 200);
    }

    /**
     * Get default tags
     * @return JsonResponse
     * @throws
     */
    public function getDefaultTags()
    {
        $userId = Auth::id();
        $tags = $this->articleService->getDefaultTags($userId);

        return response()->json($tags);
    }

    /**
     * Get default comment
     * @return JsonResponse
     * @throws
     */
    public function getDefaultComment()
    {
        $userId = Auth::id();
        $comments = $this->articleService->getDefaultComments($userId);
        return response()->json($comments);
    }
}
