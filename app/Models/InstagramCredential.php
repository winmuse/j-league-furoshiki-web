<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramCredential extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'user_id', 'fb_id', 'ig_user_id', 'ig_business_id', 'account_name', 'name', 'avatar', 'page_id', 'token',
      'ig_email', 'ig_password'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
