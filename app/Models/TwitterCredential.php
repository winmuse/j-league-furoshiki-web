<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterCredential extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id', 'token', 'secret', 'provider_id', 'account_name', 'name', 'avatar'
  ];

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }
}
