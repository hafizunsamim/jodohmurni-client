<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSwipe extends Model
{
    protected $table = 'user_swipes';

    protected $fillable = [
        'user_id',
        'target_user_id',
        'action',
    ];

    public $timestamps = false; // table ada created_at auto
}
