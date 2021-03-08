<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ArticleMedia
 * @package App\Models
 */
class ArticleMedia extends Model
{
    protected $table = "article_media";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_id', 'article_id'
    ];
}
