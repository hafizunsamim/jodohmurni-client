<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationCountry extends Model
{
    protected $table = 'location_countries';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'name',
        'flag_path'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}