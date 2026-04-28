<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateReferral extends Model
{
    protected $table = 'affiliate_referrals';

    public $timestamps = false;

    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'affiliate_code_used',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}

