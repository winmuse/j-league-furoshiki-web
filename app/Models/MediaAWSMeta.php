<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MediaAWSMeta
 * @package App\Models
 */
class MediaAWSMeta extends Model
{
    protected $table = "medias_aws_meta";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_id', 'event', 'game', 'game_date', 'game_place', 'game_time', 'home_team', 'away_team', 'players', 'subject1', 'subject2', 'subject3', 'state1', 'state2', 'state3', 'group_name', 'others'
    ];
    //

    public function media()
    {
        return $this->belongsTo('App\Models\Media');
    }

    protected $casts = [
        'game_date' => 'date',
    ];
}
