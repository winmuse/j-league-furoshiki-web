<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SnsUrl
 * 
 * @package App\Models
 * 
 * @property-read string $sns_url SNS URL HTML
 */
class SnsUrl extends Model
{
    protected $table = "sns_urls";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'credential_id', 'article_id', 'url', 'sns'
    ];

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id', 'id');
    }

    /**
     * property sns_url
     * 
     * @return string
     */
    public function getSnsUrlAttribute(): string
    {
//        if (is_null($this->provider)) return '';
//        return "<a href='" . $this->url . "' target='_blank'>" . $this->provider->provider_label . "</a>";
        if (is_null($this->sns)) return '';
        return "<a href='" . $this->url . "' target='_blank'>" . $this->sns . "</a>";
    }
}
