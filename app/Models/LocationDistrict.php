<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationDistrict extends Model
{
    protected $table = 'location_districts';

    protected $fillable = [
        'region_id',
        'code',
        'name',
    ];

    public function region()
    {
        return $this->belongsTo(LocationRegion::class, 'region_id');
    }
}
