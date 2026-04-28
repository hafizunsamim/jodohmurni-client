<?php

namespace App\Services;

use App\Models\User;

/**
 * Memilih payment gateway untuk langganan berdasarkan negara pengguna.
 * Malaysia (MY) → Toyyibpay; lain → PayPal.
 */
class PaymentGatewayService
{
    public const GATEWAY_TOYYIBPAY = 'toyyibpay';
    public const GATEWAY_PAYPAL = 'paypal';

    /** Negara yang wajib guna Toyyibpay untuk subscription */
    public const TOYYIBPAY_COUNTRY = 'MY';

    /**
     * Tentukan gateway untuk user yang diberi.
     *
     * @param  User|null  $user  User (biasanya Auth::user()). Jika null, default PayPal.
     * @return string  'toyyibpay' | 'paypal'
     */
    public function gatewayForUser(?User $user): string
    {
        if (!$user) {
            return self::GATEWAY_PAYPAL;
        }

        $country = strtoupper(trim((string) ($user->country ?? '')));

        return $country === self::TOYYIBPAY_COUNTRY
            ? self::GATEWAY_TOYYIBPAY
            : self::GATEWAY_PAYPAL;
    }

    /**
     * Semak sama ada user patut guna Toyyibpay.
     */
    public function useToyyibpay(?User $user): bool
    {
        return $this->gatewayForUser($user) === self::GATEWAY_TOYYIBPAY;
    }

    /**
     * Semak sama ada user patut guna PayPal.
     */
    public function usePayPal(?User $user): bool
    {
        return $this->gatewayForUser($user) === self::GATEWAY_PAYPAL;
    }

    /**
     * Label gateway untuk paparan UI (BM).
     */
    public function gatewayLabel(?User $user): string
    {
        return $this->useToyyibpay($user)
            ? 'Bayar dengan Toyyibpay (FPX/Kad)'
            : 'Bayar dengan PayPal';
    }
}
