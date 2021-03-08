<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginToken extends Model
{
  protected $table = "login_tokens";

  const PIN_CODE_USED = 'used';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id', 'pin', 'access_token', 'token', 'expired_at'
  ];
  //

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }
}
