<?php

namespace App\Models\Master;

use Eloquent;

class ExpireTag extends Eloquent
{

    protected $fillable = [
                            'name',
                            'type',
                            'expire_at',
                            'use_start',
                            'club_id'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'mtb_expire_tags';
    public $timestamps = false;

    /**
     * @var array
     */
    protected $casts = [
        'expire_at' => 'datetime',
        'use_start' => 'datetime'
    ];

    public function club()
    {
        return $this->belongsTo('App\Models\Admin', 'club_id', 'id');
    }
}
