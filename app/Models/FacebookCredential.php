<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookCredential extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'token', 'provider_id', 'account_name', 'name', 'avatar'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function pages()
    {
        return $this->hasMany('App\Models\FacebookPage', 'provider_id', 'provider_id');
    }
}
