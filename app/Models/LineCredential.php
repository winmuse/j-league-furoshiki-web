<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LineCredential
 * @package App\Models
 *
 * @property int user_id
 * @property string channel_secret
 * @property string access_token
 */
class LineCredential extends Model
{
  public const STATUS_ACTIVE = 1;
  public const STATUS_INACTIVE = 0;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $table = 'line_credential';

  protected $fillable = [
    'user_id',
    'channel_secret',
    'name',
    'auth_url',
    'access_token',
    'valid_flag',
  ];

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }

  /**
   * ステータス一覧
   *
   * @return array
   */
  public static function getFlagOptions(): array
  {
    return [
      static::STATUS_ACTIVE => '有効',
      static::STATUS_INACTIVE => '無効'
    ];
  }
}
