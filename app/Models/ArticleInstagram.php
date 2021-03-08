<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ArticleProvider
 * @package App\Models
 */
class ArticleInstagram extends Model
{
    protected $table = "article_instagrams";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id', 'status', 'memo', 'media_url'
    ];

    public function article()
    {
        return $this->belongsTo('App\Models\Article');
    }
}
