<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'uuid','user_id','package_id','status','currency','amount_sen',
        'affiliate_code_used','referrer_user_id','commission_percent','commission_sen',
        'started_at','ends_at'
    ];

    protected $casts = [
        'amount_sen' => 'integer',
        'commission_percent' => 'integer',
        'commission_sen' => 'integer',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'package_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
