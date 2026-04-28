<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterHobby extends Model
{
    protected $table = 'master_hobbies';

    protected $fillable = [
        'name',
        'is_active',
        'sort_order',
    ];
}
