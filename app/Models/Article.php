<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = "articles";

    protected $fillable = [
      'user_id', 'description', 'publish_at', 'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function tags()
    {
        return $this->hasMany('App\Models\Tag');
    }

    public function medias()
    {
      return $this->belongsToMany('App\Models\Media', 'article_media');
    }

    public function providers()
    {
      return $this->belongsToMany('App\Models\Provider', 'article_provider');
    }

    public function sns_urls()
    {
        return $this->hasMany('App\Models\SnsUrl');
    }
}
