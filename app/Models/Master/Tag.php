<?php

namespace App\Models\Master;

use Eloquent;

class Tag extends Eloquent
{

    protected $fillable = [
                            'name',
                            'type',
                            'club_id'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'mtb_tags';
    public $timestamps = false;

    public function club()
    {
        return $this->belongsTo('App\Models\Admin', 'club_id', 'id');
    }
}
