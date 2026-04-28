<?php

namespace App\Services;

use App\Mail\SubscriptionActivatedMail;
use App\Models\AffiliateCommission;
use App\Models\AffiliateReferral;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Services\AffiliateTierService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ToyyibpayService
{
    private string $baseUrl;
    private string $userSecretKey;
    private string $categoryCode;
    private int $cacheBillTtl;

    public function __construct()
    {
        $sandbox = config('toyyibpay.sandbox', true);
        $this->baseUrl = $sandbox
            ? 'https://dev.toyyibpay.com'
            : 'https://toyyibpay.com';
        $this->userSecretKey = (string) (config('toyyibpay.user_secret_key') ?? env('TOYYIBPAY_USER_SECRET_KEY') ?? '');
        $this->categoryCode = (string) (config('toyyibpay.category_code') ?? env('TOYYIBPAY_CATEGORY_CODE') ?? '');
        $ttl = config('toyyibpay.cache_bill_ttl_minutes', 60);
        $this->cacheBillTtl = ((int) $ttl ?: 60) * 60;
    }

    /**
     * Cipta bil baru via API createBill.
     * Amount dalam sen (e.g. 9900 = RM 99.00).
     *
     * @return array{success: bool, bill_code?: string, payment_url?: string, message?: string}
     */
    public function createBill(
        string $billName,
        string $billDescription,
        int $amountSen,
        string $billReturnUrl,
        string $billCallbackUrl,
        string $billExternalReferenceNo,
        ?string $billTo = null,
        ?string $billEmail = null,
        ?string $billPhone = null
    ): array {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Toyyibpay belum dikonfigurasi.'];
        }

        $payload = [
            'userSecretKey' => $this->userSecretKey,
            'categoryCode' => $this->categoryCode,
            'billName' => $this->sanitizeBillText($billName, 30),
            'billDescription' => $this->sanitizeBillText($billDescription, 100),
            'billPriceSetting' => 1, // fixed amount
            'billAmount' => $amountSen,
            'billReturnUrl' => $billReturnUrl,
            'billCallbackUrl' => $billCallbackUrl,
            'billExternalReferenceNo' => $billExternalReferenceNo,
            'billPayorInfo' => 1,
            'billPaymentChannel' => '2', // 0=FPX, 1=CC, 2=both
        ];

        if ($billTo !== null && $billTo !== '') {
            $payload['billTo'] = $billTo;
        }
        if ($billEmail !== null && $billEmail !== '') {
            $payload['billEmail'] = $billEmail;
        }
        if ($billPhone !== null && $billPhone !== '') {
            $payload['billPhone'] = $billPhone;
        }

        $response = Http::asForm()
            ->post("{$this->baseUrl}/index.php/api/createBill", $payload);

        if (!$response->successful()) {
            Log::warning('Toyyibpay createBill failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [
                'success' => false,
                'message' => 'Gagal cipta bil Toyyibpay. Sila cuba lagi.',
            ];
        }

        $data = $response->json();
        $body = $response->body();

        // API boleh return array of object dengan BillCode, atau format ralat lain
        $billCode = null;
        if (is_array($data) && isset($data[0]) && is_array($data[0])) {
            $billCode = $data[0]['BillCode'] ?? $data[0]['billCode'] ?? null;
        }
        if ($billCode === null || $billCode === '') {
            Log::warning('Toyyibpay createBill invalid response', [
                'body' => $body,
                'parsed' => $data,
                'content_type' => $response->header('Content-Type'),
            ]);
            // Cuba ambil mesej ralat dari API (format berbeza mengikut dokumentasi)
            $apiMessage = null;
            if (is_array($data)) {
                $first = is_array($data[0] ?? null) ? $data[0] : $data;
                $apiMessage = $first['msg'] ?? $first['message'] ?? $first['Message'] ?? $first['error'] ?? null;
            }
            if (is_string($data)) {
                $apiMessage = $data;
            }
            $message = $apiMessage
                ? 'Toyyibpay: ' . (is_string($apiMessage) ? $apiMessage : json_encode($apiMessage))
                : 'Respon Toyyibpay tidak sah. Semak TOYYIBPAY_USER_SECRET_KEY, TOYYIBPAY_CATEGORY_CODE dan TOYYIBPAY_SANDBOX dalam .env.';
            return [
                'success' => false,
                'message' => $message,
            ];
        }

        $billCode = (string) $billCode;
        $paymentUrl = rtrim($this->baseUrl, '/') . '/' . $billCode;

        return [
            'success' => true,
            'bill_code' => $billCode,
            'payment_url' => $paymentUrl,
        ];
    }

    /**
     * Sahkan hash callback dari Toyyibpay.
     * Formula: MD5( userSecretKey + status + order_id + refno + "ok" )
     */
    public function verifyCallbackHash(string $status, string $orderId, string $refno, string $receivedHash): bool
    {
        $expected = md5($this->userSecretKey . $status . $orderId . $refno . 'ok');
        return hash_equals($expected, $receivedHash);
    }

    /**
     * Simpan konteks bil (user_id, package_id) untuk callback.
     */
    public function storeBillContext(string $orderId, $userId, int $packageId): void
    {
        Cache::put("toyyibpay_bill:{$orderId}", [
            'user_id' => $userId,
            'package_id' => $packageId,
        ], $this->cacheBillTtl);
    }

    public function getBillContext(string $orderId): ?array
    {
        return Cache::get("toyyibpay_bill:{$orderId}");
    }

    public function forgetBillContext(string $orderId): void
    {
        Cache::forget("toyyibpay_bill:{$orderId}");
    }

    public function isConfigured(): bool
    {
        return $this->userSecretKey !== '' && $this->categoryCode !== '';
    }

    /**
     * Aktifkan subscription untuk order_id (dipanggil dari callback atau return URL).
     * Return true jika subscription dicipta atau sudah wujud; false jika konteks tiada/invalid.
     *
     * @param  int|null  $amountSen  Amount dalam sen dari callback; null = guna harga package
     */
    public function activateSubscriptionForOrder(string $orderId, ?int $amountSen = null): bool
    {
        $context = $this->getBillContext($orderId);
        if (! $context) {
            Log::warning('Toyyibpay activateSubscriptionForOrder: no context', ['order_id' => $orderId]);
            return false;
        }

        $userId = $context['user_id'] ?? null;
        $packageId = (int) ($context['package_id'] ?? 0);
        if (! $userId || ! $packageId) {
            $this->forgetBillContext($orderId);
            return false;
        }

        $user = User::find($userId);
        $pkg = SubscriptionPackage::where('id', $packageId)->where('is_active', 1)->first();
        if (! $user || ! $pkg) {
            Log::warning('Toyyibpay activateSubscriptionForOrder: user or package not found', [
                'user_id' => $userId,
                'package_id' => $packageId,
            ]);
            $this->forgetBillContext($orderId);
            return false;
        }

        $currentActive = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->exists();

        if ($currentActive) {
            $this->forgetBillContext($orderId);
            return true;
        }

        $amountSen = $amountSen !== null && $amountSen > 0 ? $amountSen : (int) $pkg->price_sen;
        $startedAt = now();
        $endsAt = $pkg->duration_days ? now()->addDays((int) $pkg->duration_days) : null;

        DB::transaction(function () use ($user, $pkg, $amountSen, $startedAt, $endsAt) {
            $referral = AffiliateReferral::query()
                ->where('referred_user_id', $user->id)
                ->first();

            $alreadyCommissioned = $referral
                ? AffiliateCommission::query()->where('referred_user_id', $user->id)->exists()
                : true;

            $referrerId = ($referral && !$alreadyCommissioned) ? (string) $referral->referrer_user_id : null;
            $affiliateCodeUsed = ($referral && !$alreadyCommissioned) ? (string) $referral->affiliate_code_used : null;

            $commissionSen = null;
            if ($referrerId) {
                $referrer = User::find($referrerId);
                if ($referrer) {
                    $tierSvc = new AffiliateTierService();
                    $tier = $tierSvc->tierFor($referrer);
                    $commission = $tierSvc->commissionSenForTier($tier);
                    $commissionSen = $commission > 0 ? $commission : null;
                    if ($commissionSen === null) {
                        $referrerId = null;
                        $affiliateCodeUsed = null;
                    }
                } else {
                    $referrerId = null;
                    $affiliateCodeUsed = null;
                }
            }

            $sub = Subscription::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'package_id' => $pkg->id,
                'status' => 'active',
                'currency' => $pkg->currency ?? 'MYR',
                'amount_sen' => $amountSen,
                'affiliate_code_used' => $affiliateCodeUsed,
                'referrer_user_id' => $referrerId,
                'commission_percent' => $referrerId ? 0 : null,
                'commission_sen' => $commissionSen,
                'started_at' => $startedAt,
                'ends_at' => $endsAt,
            ]);

            if ($referrerId) {
                AffiliateCommission::create([
                    'referrer_user_id' => $referrerId,
                    'referred_user_id' => $user->id,
                    'subscription_id' => $sub->id,
                    'affiliate_code_used' => $affiliateCodeUsed,
                    'commission_percent' => 0,
                    'commission_sen' => (int) $commissionSen,
                    'status' => AffiliateCommission::STATUS_PENDING,
                    'created_at' => now(),
                ]);
            }

            $newStatus = ($pkg->code ?? '') === \App\Services\EarlyBirdService::HYPE_CODE
                ? User::STATUS_KEAHLIAN_HYPE
                : (($user->status_keahlian ?? '') === User::STATUS_KEAHLIAN_GRADUATE
                    ? User::STATUS_KEAHLIAN_HYPE
                    : User::STATUS_KEAHLIAN_ACTIVE);
            $user->update(['status_keahlian' => $newStatus]);
        });

        if (($pkg->code ?? '') === \App\Services\EarlyBirdService::HYPE_CODE) {
            (new \App\Services\EarlyBirdService())->maybeDisableEarlyBirdIfCapReached();
        }

        $this->forgetBillContext($orderId);

        try {
            $ebookContent = $this->resolveEbookContent($pkg->path ?? $user->path);
            if (filled($user->email)) {
                Mail::to($user->email)->send(
                    new SubscriptionActivatedMail($user, $pkg, $ebookContent)
                );
            }
        } catch (\Throwable $e) {
            Log::error('Toyyibpay: failed to send subscription email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info('Toyyibpay subscription activated', [
            'order_id' => $orderId,
            'user_id' => $user->id,
            'package_id' => $pkg->id,
        ]);

        return true;
    }

    /**
     * @return array{link: string, paragraphs: string[]}
     */
    private function resolveEbookContent(?string $path): array
    {
        $isPoligami = in_array($path, ['poligami', 'wanita_poligami', 'wanita_terbuka'], true);
        if ($isPoligami) {
            return [
                'link' => 'https://drive.google.com/file/d/1ibpP66z19QjgxRRznXMjEecXhzF9Zy_4/view?usp=drivesdk',
                'paragraphs' => [
                    'Assalamualaikum',
                    'Bersama ini disertakan file ebook berformat "Protected PDF".',
                    'Sekian, terima kasih.',
                    "Tulus ikhlas dari\nJodohMurni",
                ],
            ];
        }
        return [
            'link' => 'https://drive.google.com/file/d/1JkYwAnJcimGP9rucBOJKUDuw47gEuW6A/view?usp=drivesdk',
            'paragraphs' => [
                'Assalamualaikum',
                'Bersama ini disertakan file ebook berformat PDF',
                'Sekian, terima kasih.',
                "Tulus ikhlas dari\nJodohMurni",
            ],
        ];
    }

    /**
     * Toyyibpay: alphanumeric, space dan underscore sahaja; max length.
     */
    private function sanitizeBillText(string $text, int $maxLen): string
    {
        $text = preg_replace('/[^A-Za-z0-9 _]/', '', $text);
        return mb_substr(trim($text), 0, $maxLen);
    }
}
