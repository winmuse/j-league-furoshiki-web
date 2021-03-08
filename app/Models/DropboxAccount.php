<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DropboxAccount
 * @package App\Models
 */
class DropboxAccount extends Model
{
    protected $table = "dropbox_accounts";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id', 'app_key', 'app_secret', '_token', 'folder'
    ];
}
