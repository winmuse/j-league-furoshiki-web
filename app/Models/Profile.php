<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = "profiles";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
//        'mobile', 'sms_token', 'token_created', 'verified_at', 'admin_id'
        'mobile', 'admin_id', 'user_id'
    ];

    public function store(Request $request)
    {
        $this->fill($request->all());
        $sms = $this->save();
        return response()->json($sms, 200);
    }

    public function updateModel(Request $request)
    {
        $this->update($request->all());
        return $this;
    }

    public function club()
    {
        return $this->belongsTo('App\Models\Admin', 'admin_id', 'id');
    }

    public function user()
    {
      return $this->belongsTo('App\Models\User');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin');
    }
}
