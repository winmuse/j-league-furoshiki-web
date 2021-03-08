<?php

namespace App\Services\Article;

use App\Models\Article;
use App\Models\FacebookPage;
use App\Models\SnsUrl;
use App\Models\Master\Comment as AdminComment;
use App\Models\Master\Tag as AdminTag;
use App\Models\Master\ExpireTag as AdminExpireTag;
use App\Models\User;
use Carbon\Carbon;
use App\Models\ArticleMedia;
use App\Models\ArticleProvider;
use App\Models\ArticleTag;

/**
 * Class ArticleServiceService
 * @package App\Http\Services\Media
 */
class ArticleService
{
    /** @var int */
    private const PER_PAGE = 20;

    /**
     * @param int $id
     * @return ArticleMedia
     */
    public function getArticleMedia($id)
    {
        return ArticleMedia::where('id', $id)->with('media')->first();
    }

    /**
     * @param int $id
     */
    public function getArticle($id)
    {
        $article = Article::with('medias')
            ->with('tags')
            ->find($id);
        if (is_null($article)) {
            return null;
        }
        $article->providers = ArticleProvider::where('article_id', $article->id)->get();
        return $article;
    }

    /**
     * @param int $user_id
     * @return Article[]
     */
    public function getDrafts($user_id)
    {
        $articles = Article::where('user_id', $user_id)
            ->where('status', 0)
            ->with('medias')
            ->with('tags')
            ->latest()
            ->get();
        $result = [];
        foreach ($articles as $article) {
            $article->providers = ArticleProvider::where('article_id', $article->id)->get();
            array_push($result, $article);
        }
        return $result;
    }

    /**
     * @param int $user_id
     * @return Article[]
     */
    public function getPublics($user_id)
    {
        $articles = Article::where('user_id', $user_id)
            ->where('status', 1)
            ->with('medias')
            ->with('tags')
            ->latest()
            ->get();
        $result = [];
        foreach ($articles as $article) {
            $article->providers = ArticleProvider::where('article_id', $article->id)->get();
            array_push($result, $article);
        }
        return $result;
    }

    /**
     * @param int $user_id
     * @param String $description
     * @param String $publish_at
     * @param int $status
     * @param array $tags
     * @return Article
     */
    public function newArticle($user_id, $description, $publish_at, $status, $tags)
    {
        $tmpDescription = $description;

        if (count($tags) > 0) {
            $tmpDescription .= "\n";
        }

        foreach ($tags as $key => $tag) {
            $tmpDescription = $tmpDescription . ($key === 0 ? '' : ' ') . '#' . $tag;
        }

        return Article::create(
            [
                'user_id'     => $user_id,
                'description' => $tmpDescription,
                'publish_at'  => $publish_at,
                'status'      => $status,
            ]
        );
    }

    /**
     * @param int $article_id
     * @param int $media_id
     */
    public function addArticleMediaRelation($article_id, $media_id)
    {
        ArticleMedia::create(
            [
                'media_id'   => $media_id,
                'article_id' => $article_id
            ]
        );
    }

    /**
     * @param int $article_id
     * @param int $tag_id
     */
    public function addArticleTagRelation($article_id, $tag_id)
    {
        ArticleTag::create(
            [
                'tag_id'     => $tag_id,
                'article_id' => $article_id
            ]
        );
    }

    /**
     * @param int $article_id
     * @param string $sns
     * @param int $credential_id
     */
    public function saveArticleCredentialRelation($article_id, $sns, $credential_id)
    {
        $provider = ArticleProvider::where(
            [
                'article_id'    => $article_id,
                'sns'           => $sns,
                'credential_id' => $credential_id
            ]
        )->first();
        if (is_null($provider)) {
            ArticleProvider::create(
                [
                    'article_id'    => $article_id,
                    'sns'           => $sns,
                    'credential_id' => $credential_id,
                ]
            );
        }
    }

    /**
     * @param int $article_id
     * @param string $sns
     * @param int $credential_id
     * @param string $sns_link
     */
    public function saveArticleUrl($article_id, $sns, $credential_id, $sns_link)
    {
        $link = SnsUrl::where(
            [
                'credential_id' => $credential_id,
                'sns'           => $sns,
                'article_id'    => $article_id,
            ]
        )->first();

        if (is_null($link)) {
            SnsUrl::create(
                [
                    'credential_id' => $credential_id,
                    'sns'           => $sns,
                    'article_id'    => $article_id,
                    'url'           => $sns_link
                ]
            );
        } else {
            if ($sns_link !== '') {
                $sns_link->url = $sns_link;
                $sns_link->save();
            }
        }
    }

    /** get default tags
     * @param int $userId
     * @return int[]
     */
    public function getDefaultTags($userId)
    {
        $clubId = User::find($userId)->profile->admin_id;
        $admin_tags = AdminTag::where('type', 'j-league')
            ->orWhere(
                function ($query) use ($clubId) {
                    $query->where('type', 'club')
                        ->where('club_id', $clubId);
                }
            )
            ->get();
        $today = Carbon::now();
        $admin_expire_tags = AdminExpireTag::where('use_start', '<=', $today)
            ->where('expire_at', '>=', $today)
            ->where(
                function ($query) use ($clubId) {
                    $query->where('type', 'j-league')
                        ->orWhere(
                            function ($query1) use ($clubId) {
                                $query1->where('type', 'club')
                                    ->where('club_id', $clubId);
                            }
                        );
                }
            )
            ->get();
        $result = [];
        foreach ($admin_tags as $t) {
            array_push($result, $t->name);
        }
        foreach ($admin_expire_tags as $t) {
            array_push($result, $t->name);
        }
        return $result;
    }

    /** get default comments
     * @param int $userId
     * @return int[]
     */
    public function getDefaultComments($userId)
    {
        $clubId = User::find($userId)->profile->admin_id;
        $comments = AdminComment::where('type', 'j-league')
            ->orWhere(
                function ($query) use ($clubId) {
                    $query->where('type', 'club')
                        ->where('club_id', $clubId);
                }
            )
            ->get();
//        return AdminComment::all();
        return $comments;
    }
}
