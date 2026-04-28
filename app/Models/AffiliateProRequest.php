<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateProRequest extends Model
{
    protected $table = 'affiliate_pro_requests';

    protected $fillable = [
        'user_id',
        'reason',
        'promotion_platform',
        'status',
        'reviewed_at',
        'reviewed_by_admin_id',
        'admin_feedback',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public static function allowedStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
        ];
    }
}

