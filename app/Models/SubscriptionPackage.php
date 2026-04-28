<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    protected $table = 'subscription_packages';

    protected $fillable = [
        'code','gender','path','poligami_level','poligami_situation',
        'name','price_sen','currency','duration_days','affiliate_percent',
        'ebook_path','is_active','sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_sen' => 'integer',
        'duration_days' => 'integer',
        'affiliate_percent' => 'integer',
        'poligami_level' => 'integer',
        'poligami_situation' => 'integer',
    ];
}
