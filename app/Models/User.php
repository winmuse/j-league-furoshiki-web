<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class SNS
 * @package App\Models
 *
 * @property-read string $status_label ユーザーステータス
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'name_en', 'player_no', 'email', 'password', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile() {
       return $this->hasOne('App\Models\Profile')->with('club');
    }

    public function facebook_credentials() {
        return $this->hasMany('App\Models\FacebookCredential');
    }

    public function twitter_credentials() {
        return $this->hasMany('App\Models\TwitterCredential');
    }

    public function instagram_credentials() {
        return $this->hasMany('App\Models\InstagramCredential');
    }

    public function line_credentials() {
        return $this->hasMany('App\Models\LineCredential');
    }

    public function articles()
    {
        return $this->hasMany('App\Models\Article');
    }

    public function club() {
        return $this->belongsTo('App\Models\Admin', 'profiles');
    }

    public function login_tokens()
    {
        return $this->hasMany('App\Models\LoginToken');
    }

    public function facebook_pages()
    {
        return $this->hasMany('App\Models\FacebookPage');
    }

    /**
     * ユーザーステータス一覧
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            static::STATUS_ACTIVE => '有効',
            static::STATUS_INACTIVE => '無効'
        ];
    }

    /**
     * ユーザーステータス
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusOptions()[$this->status];
    }

}
