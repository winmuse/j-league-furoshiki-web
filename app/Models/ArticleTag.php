<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ArticleTag
 * @package App\Models
 */
class ArticleTag extends Model
{
    protected $table = "article_tag";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_id', 'article_id'
    ];

    public function tag()
    {
        return $this->belongsTo('App\Models\Tag');
    }

    public function article()
    {
        return $this->belongsTo('App\Models\Article');
    }
}
