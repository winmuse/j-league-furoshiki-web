<?php

namespace App\Services\Article;

use App\Http\Controllers\Api\ArticleController;
use App\Models\ArticleInstagram;

/**
 * Class ArticleServiceService
 * @package App\Http\Services\Media
 */
class ArticleInstagramService
{
    /**
     * @param int $article_id
     * @param string $media_link
     * @return ArticleInstagram
     */
    public function newArticleInstagram($article_id, $media_link)
    {
        return ArticleInstagram::create([
            'article_id' => $article_id,
            'media_url' => $media_link
        ]);
    }
}
