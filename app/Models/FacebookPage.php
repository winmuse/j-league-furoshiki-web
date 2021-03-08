<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookPage extends Model
{
  protected $table = 'facebook_pages';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id', 'provider_id', 'page_id', 'access_token', 'name', 'category'
  ];

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }
}
