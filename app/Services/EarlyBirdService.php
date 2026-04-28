<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;

class EarlyBirdService
{
    public const SETTING_ACTIVE = 'is_early_bird_active';
    public const HYPE_CODE = 'HYPE';

    /** Bilangan maksimum pengguna yang boleh subscribe pakej early bird (HYPE). */
    public function getMaxEarlyBirdUsers(): int
    {
        return (int) config('earlybird.max_users', 300);
    }

    /**
     * Early Bird boleh guna .env (override) atau jadual settings.
     */
    public function isEarlyBirdActive(): bool
    {
        if (config('earlybird.force_active') !== null) {
            return (bool) config('earlybird.force_active');
        }
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return (bool) config('earlybird.is_early_bird_active', false);
        }
        $v = Setting::get(self::SETTING_ACTIVE, config('earlybird.is_early_bird_active', '0'));

        return in_array($v, ['1', 'true', true], true);
    }

    /**
     * Bilangan pengguna yang telah subscribe pakej early bird (HYPE) dengan subscription aktif.
     * Kira distinct user_id yang ada subscription aktif ke pakej HYPE.
     */
    public function getEarlyBirdSubscriberCount(): int
    {
        $hypePackage = SubscriptionPackage::where('code', self::HYPE_CODE)->first();
        if (!$hypePackage) {
            return 0;
        }

        return (int) Subscription::where('package_id', $hypePackage->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->count(\Illuminate\Support\Facades\DB::raw('DISTINCT user_id'));
    }

    /**
     * Adakah Early Bird sedang berjalan: is_early_bird_active ON dan bilangan subscriber < max (300).
     * Jika bilangan pengguna yang subscribe pakej early bird sudah 300, early bird automatik tamat.
     */
    public function isEarlyBirdRunning(): bool
    {
        if (!$this->isEarlyBirdActive()) {
            return false;
        }

        return $this->getEarlyBirdSubscriberCount() < $this->getMaxEarlyBirdUsers();
    }

    /**
     * Jika bilangan subscriber early bird sudah mencapai max, set is_early_bird_active kepada off.
     * Panggil selepas aktivasi subscription pakej HYPE.
     */
    public function maybeDisableEarlyBirdIfCapReached(): void
    {
        if ($this->getEarlyBirdSubscriberCount() >= $this->getMaxEarlyBirdUsers()) {
            Setting::set(self::SETTING_ACTIVE, '0');
        }
    }

    /**
     * Jika Early Bird running: return hanya pakej HYPE (atau collection).
     * Jika tidak: return collection kosong untuk "filter HYPE keluar" bila perlu, atau null = jangan filter.
     */
    public function getHypePackage(): ?SubscriptionPackage
    {
        return SubscriptionPackage::where('code', self::HYPE_CODE)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * True = papar hanya pakej HYPE di checkout.
     */
    public function shouldShowOnlyHypePackage(): bool
    {
        return $this->isEarlyBirdRunning();
    }

    /**
     * True = sembunyikan pakej HYPE (bila Early Bird off atau cap subscriber dicapai).
     */
    public function shouldHideHypePackage(): bool
    {
        if ($this->isEarlyBirdActive() && $this->isEarlyBirdRunning()) {
            return false;
        }

        return true;
    }
}
