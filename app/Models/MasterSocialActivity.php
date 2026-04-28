<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSocialActivity extends Model
{
    protected $table = 'master_social_activities';

    protected $fillable = [
        'name',
        'is_active',
        'sort_order',
    ];
}
