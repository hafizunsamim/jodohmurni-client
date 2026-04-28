<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPreference extends Model
{
    use HasFactory;

    protected $table = 'user_preferences';

    protected $fillable = [
        'user_id',
        'age_min',
        'age_max',
        'location_radius',
        'willing_to_relocate',


    ];

    protected $casts = [
        'user_id' => 'string',
    ];

    public function user()
    {
        // foreign key = user_id, owner key = id (UUID)
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
