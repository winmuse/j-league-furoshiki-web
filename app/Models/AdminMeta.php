<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminMeta
 * @package App\Models
 */
class AdminMeta extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'mapped_name'
    ];

    public $timestamps = false;
}
