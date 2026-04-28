<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private string $baseUrl;

    private string $clientId;

    private string $secret;

    private string $currency;

    private int $cacheOrderTtl;

    public function __construct()
    {
        // Baca dari config dulu; fallback ke env() supaya nilai .env sentiasa dipakai
        $this->clientId = (string) (config('paypal.client_id') ?? env('PAYPAL_CLIENT_ID') ?? '');
        $this->secret = (string) (config('paypal.secret') ?? env('PAYPAL_SECRET') ?? '');
        $this->currency = (string) (config('paypal.currency') ?? env('PAYPAL_CURRENCY') ?? 'MYR');
        $ttl = config('paypal.cache_order_ttl_minutes') ?? env('PAYPAL_CACHE_ORDER_TTL', 60);
        $this->cacheOrderTtl = ((int) $ttl ?: 60) * 60;

        $mode = config('paypal.mode') ?? env('PAYPAL_MODE', 'sandbox');
        $this->baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Dapatkan OAuth2 access token dari PayPal.
     */
    public function getAccessToken(): ?string
    {
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            Log::warning('PayPal getAccessToken failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();
        return $data['access_token'] ?? null;
    }

    /**
     * Cipta PayPal order (amount dalam sen, kita tukar ke unit untuk PayPal).
     *
     * @param  int  $amountSen  Jumlah dalam sen (e.g. 9900 = RM 99.00)
     * @param  string  $description  Penerangan order (e.g. nama package)
     * @param  string  $returnUrl  URL selepas user selesai bayar di PayPal
     * @param  string  $cancelUrl  URL jika user batal
     */
    public function createOrder(
        int $amountSen,
        string $description,
        string $returnUrl,
        string $cancelUrl
    ): ?array {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        $value = number_format($amountSen / 100, 2, '.', '');

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'description' => $description,
                        'amount' => [
                            'currency_code' => $this->currency,
                            'value' => $value,
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                    'brand_name' => config('app.name'),
                    'user_action' => 'PAY_NOW',
                    // Allow all funding: PayPal balance, linked card, or guest card.
                    'payment_method' => [
                        'payee_preferred' => 'UNRESTRICTED',
                    ],
                ],
            ]);

        if (!$response->successful()) {
            $body = $response->body();
            $decoded = json_decode($body, true);
            Log::warning('PayPal createOrder failed', [
                'status' => $response->status(),
                'body' => $body,
                'details' => $decoded['details'] ?? null,
                'message' => $decoded['message'] ?? null,
                'name' => $decoded['name'] ?? null,
            ]);
            return null;
        }

        $data = $response->json();
        $orderId = $data['id'] ?? null;
        if (!$orderId) {
            return null;
        }

        $links = $data['links'] ?? [];
        $approveUrl = null;
        foreach ($links as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                $approveUrl = $link['href'] ?? null;
                break;
            }
        }

        return [
            'id' => $orderId,
            'approve_url' => $approveUrl,
            'status' => $data['status'] ?? null,
        ];
    }

    /**
     * Capture order selepas user approve di PayPal.
     */
    public function captureOrder(string $orderId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        // PayPal memerlukan body JSON kosong {} dan Content-Type: application/json
        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->withBody('{}', 'application/json')
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        if (!$response->successful()) {
            Log::warning('PayPal captureOrder failed', [
                'order_id' => $orderId,
                'http_status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $response->json();
    }

    /**
     * Dapatkan butiran order (untuk semak status bila capture mungkin sudah berlaku).
     */
    public function getOrder(string $orderId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }
        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

        if (!$response->successful()) {
            return null;
        }
        return $response->json();
    }

    /**
     * Semak sama ada response capture menandakan bayaran berjaya.
     * PayPal boleh return status di root atau dalam purchase_units.
     */
    public function isCaptureSuccessful(array $captureResponse): bool
    {
        $status = strtoupper((string) ($captureResponse['status'] ?? ''));
        if ($status === 'COMPLETED') {
            return true;
        }
        $units = $captureResponse['purchase_units'] ?? [];
        foreach ($units as $unit) {
            $captures = $unit['payments']['captures'][0] ?? null;
            if ($captures && strtoupper((string) ($captures['status'] ?? '')) === 'COMPLETED') {
                return true;
            }
        }
        return false;
    }

    /**
     * Simpan maklumat order (user_id, package_id) dalam cache untuk rujukan bila capture.
     * user_id boleh string (UUID) atau int mengikut model User.
     */
    public function storeOrderContext(string $orderId, string|int $userId, int $packageId): void
    {
        Cache::put("paypal_order:{$orderId}", [
            'user_id' => $userId,
            'package_id' => $packageId,
        ], $this->cacheOrderTtl);
    }

    /**
     * Ambil maklumat order dari cache (tanpa padam).
     */
    public function getOrderContext(string $orderId): ?array
    {
        return Cache::get("paypal_order:{$orderId}");
    }

    /**
     * Buang maklumat order dari cache (panggil selepas aktivasi berjaya).
     */
    public function forgetOrderContext(string $orderId): void
    {
        Cache::forget("paypal_order:{$orderId}");
    }

    /**
     * Ambil dan buang maklumat order dari cache.
     */
    public function getAndForgetOrderContext(string $orderId): ?array
    {
        $data = $this->getOrderContext($orderId);
        if ($data !== null) {
            $this->forgetOrderContext($orderId);
        }
        return $data;
    }

    /**
     * Semak sama ada PayPal dikonfigurasi (client_id & secret ada).
     */
    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->secret !== '';
    }
}
