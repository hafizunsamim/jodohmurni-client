<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateCommission extends Model
{
    protected $table = 'affiliate_commissions';

    public $timestamps = false;

    protected $fillable = [
        'referrer_user_id','referred_user_id','subscription_id',
        'affiliate_code_used','commission_percent','commission_sen','status','created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';
}
