<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationRegion extends Model
{
    protected $table = 'location_regions';

    protected $fillable = [
        'country_code', // guna code, bukan nama
        'code',
        'name',
    ];

    public function country()
    {
        return $this->belongsTo(LocationCountry::class, 'country_code', 'code');
    }

    public function districts()
    {
        return $this->hasMany(LocationDistrict::class, 'region_id');
    }
}