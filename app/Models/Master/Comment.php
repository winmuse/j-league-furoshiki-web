<?php

namespace App\Models\Master;

use Eloquent;

class Comment extends Eloquent
{

    protected $fillable = [
                            'name',
                            'type',
                            'club_id'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'mtb_comments';
    public $timestamps = false;
    
    public function club()
    {
        return $this->belongsTo('App\Models\Admin', 'club_id', 'id');
    }
}
