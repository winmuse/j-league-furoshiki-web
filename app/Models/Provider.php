<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Provider
 * @package App\Models
 *
 * @property-read string $provider_label Provider Label
 */
class Provider extends Model
{
    public const FACEBOOK = 'facebook';
    public const TWITTER = 'twitter';
    public const INSTAGRAM = 'instagram';
    public const LINE = 'line';

    /**
     * Articles
     * 
     */
    public function articles()
    {
        return $this->belongsToMany('App\Models\Article', 'article_provider');
    }

    /**
     * Sources
     *
     * @return array
     */
    public static function getSources(): array
    {
        return [
            static::FACEBOOK => 'Facebook',
            static::TWITTER => 'Twitter',
            static::INSTAGRAM => 'Instagram',
            static::LINE => 'Line'
        ];
    }

    /**
     * Provider Label
     *
     * @return string
     */
    public function getProviderLabelAttribute(): string
    {
        return static::getSources()[$this->name];
    }
}
