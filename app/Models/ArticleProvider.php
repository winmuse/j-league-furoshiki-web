<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ArticleProvider
 * @package App\Models
 */
class ArticleProvider extends Model
{
    protected $table = "article_provider";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'credential_id', 'article_id', 'sns'
    ];
}
