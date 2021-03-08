<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class Admin
 * @package App\Models
 *
 * @property-read string $role_label ユーザー区分
 */
class Admin extends Authenticatable
{
    use Notifiable;

    // protected $guard = 'admin';

    public const BALZ_ROLE = 'balz';
    public const JLEAGUE_ROLE = 'j-league';
    public const CLUB_ROLE = 'club';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'name_short', 'name_en', 'parent_admin_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function dropbox()
    {
        return $this->hasOne('App\Models\DropboxAccount');
    }

    /**
     * 区分一覧
     *
     * @return array
     */
    public static function getRoleOptions(): array
    {
        return [
            static::JLEAGUE_ROLE => 'Jリーグ',
            static::CLUB_ROLE => 'クラブ'
        ];
    }

    /**
     * 選手一覧
     *
     * @return
     */
    public function players()
    {
        return $this->belongsToMany('App\Models\User', 'profiles');
    }

    /**
     * ユーザーステータス
     *
     * @return string
     */
    public function getRoleLabelAttribute(): string
    {
        if ($this->role === static::BALZ_ROLE) return '運用者（バルズ）';

        return $this->getRoleOptions()[$this->role];
    }

    public function medias()
    {
        return $this->hasMany('App\Models\Media', 'club_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Admin', 'parent_admin_id', 'id');
    }

    public function profiles()
    {
        return $this->hasMany('App\Models\Profile', 'admin_id', 'id');
    }
}
