<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Media
 * @package App\Models
 *
 * @property-read string $source_label Source
 * @property-read string $thumb_url ThumbUrl
 * @property-read string $source_url SourceURL
 * @property-read string $video_url VideoURL
 * @property-read string $done_label Done Label
 */
class Media extends Model
{
    protected $table = "medias";

    public const AWS_SOURCE = 'aws';
    public const DROPBOX_SOURCE = 'dropbox';
    public const EXTENSION_JPG = 'jpg';
    public const EXTENSION_MP4 = 'mp4';

    public const DONE = 1;
    public const NOT_DONE = 0;

    public const SHOW = 1;
    public const NOT_SHOW = 0;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'creator', 'filename', 'folder', 'source', 'uploaded_at', 'extension', 'club_id', 'is_done', 'is_top',
        'updated_at', 'created_at', 'is_show'
    ];

    public $appends = [
        'source_label',
        'thumb_url',
        'source_url',
        'video_url',
    ];

    public function meta()
    {
        return $this->hasOne('App\Models\MediaAWSMeta');
    }

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Source
     *
     * @return string
     */
    public function getSourceLabelAttribute(): string
    {
        return $this->getSources()[$this->source];
    }

    /**
     * ThumbUrl
     *
     * @return string
     */
    public function getThumbUrlAttribute(): string
    {
        if ($this->source === static::AWS_SOURCE) {
            $uri = config('values.AWS_CF_URL_FOR_APP') . '/thumbnail/photos/' . $this->folder . '/' . $this->filename . '.jpg';
            return asset($uri);
        }

        if ($this->source === static::DROPBOX_SOURCE) {
            if ($this->extension === static::EXTENSION_MP4) {
                $uri = config('values.AWS_CF_URL_FOR_APP') . '/thumbnail/videos/dropbox/club_' . $this->club_id . str_replace('.mp4', '.jpg', $this->filename);
                return asset($uri);
            } else {
                return str_replace(['?dl=0', 'www.dropbox.com'], ['', 'dl.dropboxusercontent.com'], $this->folder);
            }
        }

        return '';
    }

    /**
     * Tags
     * @return string
     */
    public function getTagsAttribute(): string
    {

        if ($this->source === static::AWS_SOURCE) {
            return '/storage/photos/' . $this->folder . '/' . $this->filename . '.jpg';
        }

        if ($this->source === static::DROPBOX_SOURCE) {
            if ($this->extension === static::EXTENSION_MP4) {
                return '/images/video.jpg';
            } else {
                return str_replace(['?dl=0', 'www.dropbox.com'], ['', 'dl.dropboxusercontent.com'], $this->folder);
            }
        }

        return '';
    }

    /**
     * SourceURL
     * @return string
     */
    public function getSourceUrlAttribute(): string
    {
        if ($this->source === static::AWS_SOURCE) {
            return config('values.AWS_URL') . '/' . $this->folder . '/' . $this->filename . '.jpg';
        }

        if ($this->source === static::DROPBOX_SOURCE) {
            if ($this->extension === static::EXTENSION_MP4) {
                return '/images/video.jpg';
            } else {
                return str_replace(['?dl=0', 'www.dropbox.com'], ['', 'dl.dropboxusercontent.com'], $this->folder);
            }
        }

        return '';
    }

    /**
     * VideoURL
     *
     * @return string
     */
    public function getVideoUrlAttribute(): string
    {
        if ($this->source === static::DROPBOX_SOURCE) {
            return str_replace("www.dropbox.com", "dl.dropboxusercontent.com", $this->folder);
        }

        return '';
    }

    /**
     * Sources
     *
     * @return array
     */
    public static function getSources(): array
    {
        return [
            static::AWS_SOURCE => 'AWS',
            static::DROPBOX_SOURCE => 'Dropbox'
        ];
    }

    /**
     * Done Label
     *
     * @return string
     */
    public function getDoneLabelAttribute(): string
    {
        return static::getDones()[$this->is_done];
    }

    /**
     * Dones
     *
     * @return array
     */
    public static function getDones(): array
    {
        return [
            static::DONE => '完了',
            static::NOT_DONE => '未完了'
        ];
    }

    public static function getSortOptions(): array
    {
        return [
            'event' => 'イベント名',
            'name' => 'クラブ名',
            'players' => '選手名',
            'updated_at' => '更新日',
            'uploaded_at' => '登録日'
        ];
    }

    public static function getSortValueOptions(): array
    {
        return [
            'asc' => '昇順',
            'desc' => '降順'
        ];
    }

    public function club()
    {
        return $this->belongsTo('App\Models\Admin', 'club_id', 'id');
    }

    public function articles()
    {
        return $this->belongsToMany('App\Models\Article', 'article_media')->with('sns_urls')->with('user');
    }

    public function clubArticles()
    {
        $adminID = is_null(auth()->user()->parent_admin_id) ? auth()->user()->id : auth()->user()->parent_admin_id;

        return $this->belongsToMany('App\Models\Article', 'article_media')
                ->whereIn('articles.user_id', function ($query3) use ($adminID) {
                    $query3->select('user_id')
                        ->from(with(new \App\Models\Profile)->getTable())
                        ->where('admin_id', $adminID);
                })->with('sns_urls')->with('user');
    }

    public static function getStaticThumbUrl($item): string
    {
        if ($item->source === static::AWS_SOURCE) {
            $uri = config('values.AWS_CF_URL_FOR_APP') . '/thumbnail/photos/' . $item->folder . '/' . $item->filename . '.jpg';
            return asset($uri);
        }

        if ($item->source === static::DROPBOX_SOURCE) {
            if ($item->extension === static::EXTENSION_MP4) {
                $uri = config('values.AWS_CF_URL_FOR_APP') . '/thumbnail/videos/dropbox/club_' . $item->club_id . str_replace('.mp4', '.jpg', $item->filename);
                return asset($uri);
            } else {
                return str_replace(['?dl=0', 'www.dropbox.com'], ['', 'dl.dropboxusercontent.com'], $item->folder);
            }
        }

        return '';
    }

    public static function getStaticSourceUrl($item): string
    {
        if ($item->source === static::AWS_SOURCE) {
            return config('values.AWS_URL') . '/' . $item->folder . '/' . $item->filename . '.jpg';
        }

        if ($item->source === static::DROPBOX_SOURCE) {
            if ($item->extension === static::EXTENSION_MP4) {
                return '/images/video.jpg';
            } else {
                return str_replace(['?dl=0', 'www.dropbox.com'], ['', 'dl.dropboxusercontent.com'], $item->folder);
            }
        }

        return '';
    }
}
