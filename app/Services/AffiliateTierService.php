<?php

namespace App\Services;

use App\Models\AffiliateProRequest;
use App\Models\User;

class AffiliateTierService
{
    public const TIER_NONE = 'none';
    public const TIER_STANDARD = 'standard';
    public const TIER_PRO = 'pro';

    /**
     * @return array{enabled:bool,tier:string,commission_sen:int,commission_label:string,can_apply_pro:bool}
     */
    public function profileFor(User $user): array
    {
        $tier = $this->tierFor($user);
        $enabled = $tier !== self::TIER_NONE;
        $commissionSen = $this->commissionSenForTier($tier);

        return [
            'enabled' => $enabled,
            'tier' => $tier,
            'commission_sen' => $commissionSen,
            'commission_label' => 'RM ' . number_format($commissionSen / 100, 2),
            'can_apply_pro' => $this->canApplyPro($user, $tier),
        ];
    }

    public function tierFor(User $user): string
    {
        // External affiliate (created via public application) is always PRO
        if (!empty($user->is_external_affiliate)) {
            return self::TIER_PRO;
        }

        $m = strtoupper((string) ($user->status_keahlian ?? User::STATUS_KEAHLIAN_LITE));

        // LITE: affiliate off
        if ($m === User::STATUS_KEAHLIAN_LITE) {
            return self::TIER_NONE;
        }

        // HYPE: auto PRO (including early bird HYPE)
        if ($m === User::STATUS_KEAHLIAN_HYPE) {
            return self::TIER_PRO;
        }

        // ACTIVE/GRADUATE: standard unless approved
        if ($m === User::STATUS_KEAHLIAN_ACTIVE || $m === User::STATUS_KEAHLIAN_GRADUATE) {
            return $this->isUserProApproved($user) ? self::TIER_PRO : self::TIER_STANDARD;
        }

        // default safe fallback
        return self::TIER_NONE;
    }

    public function isUserProApproved(User $user): bool
    {
        return !empty($user->affiliate_pro_approved_at);
    }

    public function commissionSenForTier(string $tier): int
    {
        if ($tier === self::TIER_PRO) {
            return 1000;
        }
        if ($tier === self::TIER_STANDARD) {
            return 500;
        }
        return 0;
    }

    public function canApplyPro(User $user, ?string $tier = null): bool
    {
        $tier = $tier ?? $this->tierFor($user);
        $m = strtoupper((string) ($user->status_keahlian ?? User::STATUS_KEAHLIAN_LITE));

        // Only ACTIVE/GRADUATE, only when still standard
        if (!in_array($m, [User::STATUS_KEAHLIAN_ACTIVE, User::STATUS_KEAHLIAN_GRADUATE], true)) {
            return false;
        }

        if ($tier !== self::TIER_STANDARD) {
            return false;
        }

        // Block if pending request exists
        $hasPending = AffiliateProRequest::query()
            ->where('user_id', $user->id)
            ->where('status', AffiliateProRequest::STATUS_PENDING)
            ->exists();

        return !$hasPending;
    }
}

