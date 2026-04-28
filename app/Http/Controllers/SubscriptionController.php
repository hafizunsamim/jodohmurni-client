<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionActivatedMail;
use App\Models\AffiliateCommission;
use App\Models\AffiliateReferral;
use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Services\EarlyBirdService;
use App\Services\AffiliateTierService;
use App\Services\PaymentGatewayService;
use App\Services\PayPalService;
use App\Services\ToyyibpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    private const MONOGAMI_EBOOK_LINK = 'https://drive.google.com/file/d/1JkYwAnJcimGP9rucBOJKUDuw47gEuW6A/view?usp=drivesdk';
    private const POLIGAMI_EBOOK_LINK = 'https://drive.google.com/file/d/1ibpP66z19QjgxRRznXMjEecXhzF9Zy_4/view?usp=drivesdk';

    /**
     * Page subscription: list package ikut user
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $gender = $user->gender ?? null; // 'male'/'female'
        $path   = $user->path ?? null;   // 'monogami'/'poligami'/'wanita_monogami'/'wanita_poligami'/'wanita_terbuka'

        // jika schema user ada field ni (kalau tak ada, dia jadi null sahaja)
        $poligamiLevel     = $user->poligami_level ?? null;      // wanita_poligami / wanita_terbuka
        $poligamiSituation = $user->poligami_situation ?? null;  // lelaki poligami (1/2/3)

        $activeSub = Subscription::with('package')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        $stillActive = false;
        if ($activeSub && $activeSub->ends_at) {
            $stillActive = now()->lt($activeSub->ends_at);
        }

        $earlyBird = new EarlyBirdService();

        if ($earlyBird->shouldShowOnlyHypePackage()) {
            $hypePackage = $earlyBird->getHypePackage();
            $packages = $hypePackage ? collect([$hypePackage]) : collect();
        } else {
            $q = SubscriptionPackage::query()
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('price_sen');

            if ($earlyBird->shouldHideHypePackage()) {
                $q->where('code', '!=', EarlyBirdService::HYPE_CODE);
            }

            if ($gender) {
                $q->where(function ($qq) use ($gender) {
                    $qq->where('gender', $gender)->orWhere('gender', 'any');
                });
            }
            if ($path) {
                $q->where(function ($qq) use ($path) {
                    $qq->whereNull('path')->orWhere('path', $path);
                });
            }
            if ($gender === 'male' && $path === 'poligami' && $poligamiSituation) {
                $q->where('poligami_situation', (int) $poligamiSituation);
            }
            if ($gender === 'female' && in_array($path, ['wanita_poligami','wanita_terbuka'], true)) {
                if ($poligamiLevel) {
                    $q->where('poligami_level', (int) $poligamiLevel);
                } else {
                    $q->whereRaw('1=0');
                }
            }

            $packages = $q->get();
        }

        $paypalConfigured = (new PayPalService)->isConfigured();
        $toyyibpayConfigured = (new ToyyibpayService)->isConfigured();
        $gatewayService = new PaymentGatewayService;
        $paymentGateway = $gatewayService->gatewayForUser($user);
        $useToyyibpay = $gatewayService->useToyyibpay($user);
        $usePayPal = $gatewayService->usePayPal($user);
        $gatewayLabel = $gatewayService->gatewayLabel($user);
        $paymentBypass = $this->isPaymentBypassEnabled();

        return view('subscription.index', compact(
            'packages', 'activeSub', 'stillActive', 'user',
            'paypalConfigured', 'toyyibpayConfigured',
            'paymentGateway', 'useToyyibpay', 'usePayPal', 'gatewayLabel',
            'paymentBypass'
        ));
    }

    /**
     * Cipta PayPal order untuk package (JSON response untuk frontend).
     */
    public function createPayPalOrder(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'package_id' => ['required', 'integer', 'exists:subscription_packages,id'],
        ]);

        $pkg = SubscriptionPackage::where('id', $request->package_id)
            ->where('is_active', 1)
            ->firstOrFail();

        $earlyBird = new EarlyBirdService();
        if ($earlyBird->shouldHideHypePackage() && ($pkg->code ?? '') === EarlyBirdService::HYPE_CODE) {
            return response()->json(['success' => false, 'message' => 'Pakej Early Bird tidak tersedia pada masa ini.'], 403);
        }

        $this->guardPackageForUser($user, $pkg);

        $currentActive = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();
        if ($currentActive && $currentActive->ends_at && now()->lt($currentActive->ends_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih mempunyai subscription aktif.',
            ], 422);
        }

        if ($this->isPaymentBypassEnabled()) {
            return $this->jsonBypassSubscriptionActivate($user, $pkg);
        }

        $paypal = new PayPalService;
        if (!$paypal->isConfigured()) {
            return response()->json(['success' => false, 'message' => 'PayPal belum dikonfigurasi.'], 503);
        }

        $returnUrl = route('subscription.paypal.return');
        $cancelUrl = route('subscription.paypal.cancel');
        $description = $pkg->name . ' - ' . config('app.name');

        $result = $paypal->createOrder(
            (int) $pkg->price_sen,
            $description,
            $returnUrl,
            $cancelUrl
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal cipta pesanan PayPal. Sila cuba lagi.',
            ], 502);
        }

        $paypal->storeOrderContext($result['id'], $user->id, (int) $pkg->id);

        return response()->json([
            'success' => true,
            'orderId' => $result['id'],
            'approveUrl' => $result['approve_url'],
        ]);
    }

    /**
     * Cipta bil Toyyibpay untuk package (pengguna Malaysia). JSON response untuk frontend.
     */
    public function createToyyibpayBill(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'package_id' => ['required', 'integer', 'exists:subscription_packages,id'],
        ]);

        $pkg = SubscriptionPackage::where('id', $request->package_id)
            ->where('is_active', 1)
            ->firstOrFail();

        $earlyBird = new EarlyBirdService();
        if ($earlyBird->shouldHideHypePackage() && ($pkg->code ?? '') === EarlyBirdService::HYPE_CODE) {
            return response()->json(['success' => false, 'message' => 'Pakej Early Bird tidak tersedia pada masa ini.'], 403);
        }

        $this->guardPackageForUser($user, $pkg);

        $currentActive = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();
        if ($currentActive && $currentActive->ends_at && now()->lt($currentActive->ends_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih mempunyai subscription aktif.',
            ], 422);
        }

        if ($this->isPaymentBypassEnabled()) {
            return $this->jsonBypassSubscriptionActivate($user, $pkg);
        }

        $toyyibpay = new ToyyibpayService;
        if (!$toyyibpay->isConfigured()) {
            return response()->json(['success' => false, 'message' => 'Toyyibpay belum dikonfigurasi.'], 503);
        }

        $orderId = 'jm_' . Str::uuid()->toString();
        $billReturnUrl = route('subscription.toyyibpay.return', ['order_id' => $orderId]);
        $billCallbackUrl = route('subscription.toyyibpay.callback');

        $result = $toyyibpay->createBill(
            $pkg->name,
            $pkg->name . ' - ' . config('app.name'),
            (int) $pkg->price_sen,
            $billReturnUrl,
            $billCallbackUrl,
            $orderId,
            $user->name,
            $user->email,
            $user->phone
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal cipta bil Toyyibpay. Sila cuba lagi.',
            ], 502);
        }

        $toyyibpay->storeBillContext($orderId, $user->id, (int) $pkg->id);

        return response()->json([
            'success' => true,
            'orderId' => $orderId,
            'paymentUrl' => $result['payment_url'],
        ]);
    }

    /**
     * Return URL selepas user selesai bayar di Toyyibpay (redirect dengan status_id, billcode, order_id).
     * Jika callback (webhook) tidak sampai (e.g. localhost), kita aktifkan subscription di sini.
     */
    public function toyyibpayReturn(Request $request)
    {
        $statusId = $request->query('status_id');
        $orderId = $request->query('order_id');

        if ($statusId === '1') {
            $toyyibpay = new ToyyibpayService;
            $context = $toyyibpay->getBillContext($orderId ?? '');
            // Hanya aktifkan jika konteks wujud dan user login sama dengan pemilik order (fallback bila callback tak sampai e.g. localhost)
            if ($orderId && $context && (string) Auth::id() === (string) ($context['user_id'] ?? '')) {
                $toyyibpay->activateSubscriptionForOrder($orderId, null);
            }

            return redirect()
                ->route('subscription.ebook.links')
                ->with('success', 'Pembayaran berjaya. Subscription diaktifkan. Sila semak email untuk link ebook.');
        }

        if ($statusId === '2') {
            return redirect()
                ->route('subscription.index')
                ->with('info', 'Pembayaran masih dalam proses. Kami akan mengaktifkan subscription setelah bayaran disahkan.');
        }

        return redirect()
            ->route('subscription.index')
            ->withErrors(['toyyibpay' => 'Pembayaran tidak berjaya atau dibatalkan. Sila cuba lagi.']);
    }

    /**
     * Return URL selepas user selesai bayar di PayPal (PayPal hantar token=orderId).
     */
    public function paypalReturn(Request $request)
    {
        $orderId = $request->query('token');
        if (!$orderId) {
            return redirect()->route('subscription.index')->withErrors([
                'paypal' => 'Maklumat pembayaran tidak sah.',
            ]);
        }

        $paypal = new PayPalService;
        $context = $paypal->getOrderContext($orderId);
        if (!$context) {
            return redirect()->route('subscription.index')->withErrors([
                'paypal' => 'Sesi pembayaran tamat atau tidak sah. Sila cuba lagi.',
            ]);
        }

        $capture = $paypal->captureOrder($orderId);
        $captureOk = $capture && $paypal->isCaptureSuccessful($capture);

        if (!$captureOk && $capture === null) {
            // Mungkin order sudah di-capture (e.g. user refresh). Semak status order.
            $order = $paypal->getOrder($orderId);
            if ($order && strtoupper((string) ($order['status'] ?? '')) === 'COMPLETED') {
                $captureOk = true;
            }
        }
        if (!$captureOk) {
            if ($capture !== null) {
                $details = $capture['details'] ?? [];
                $processorResponse = null;
                foreach (($capture['purchase_units'] ?? []) as $unit) {
                    $cap = $unit['payments']['captures'][0] ?? null;
                    if ($cap && isset($cap['processor_response'])) {
                        $processorResponse = $cap['processor_response'];
                        break;
                    }
                }
                Log::warning('PayPal capture status not COMPLETED', [
                    'order_id' => $orderId,
                    'response_status' => $capture['status'] ?? 'null',
                    'details' => $details,
                    'processor_response' => $processorResponse,
                    'full_response' => $capture,
                ]);
            } else {
                Log::warning('PayPal captureOrder returned null', ['order_id' => $orderId]);
            }
            return redirect()->route('subscription.index')->withErrors([
                'paypal' => 'Pembayaran tidak berjaya. Sila cuba lagi atau hubungi kami.',
            ]);
        }

        $paypal->forgetOrderContext($orderId);

        $user = \App\Models\User::find($context['user_id']);
        $pkg = SubscriptionPackage::where('id', $context['package_id'])->where('is_active', 1)->first();
        if (!$user || !$pkg) {
            return redirect()->route('subscription.index')->withErrors([
                'paypal' => 'Data package tidak dijumpai. Sila hubungi kami.',
            ]);
        }

        $this->activateSubscriptionForUser($user, $pkg);

        $ebookContent = $this->resolveEbookContent($pkg->path ?? $user->path);
        $emailSent = false;
        if (filled($user->email)) {
            try {
                Mail::to($user->email)->send(
                    new SubscriptionActivatedMail($user, $pkg, $ebookContent)
                );
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error('Gagal hantar email subscription activated.', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('subscription.ebook.links')
            ->with(
                $emailSent ? 'success' : 'warning',
                $emailSent
                    ? 'Pembayaran berjaya. Subscription diaktifkan. Email ebook telah dihantar.'
                    : 'Pembayaran berjaya. Subscription diaktifkan. Link ebook ada di halaman ini.'
            );
    }

    /**
     * Cancel URL bila user batal di PayPal.
     */
    public function paypalCancel(Request $request)
    {
        return redirect()
            ->route('subscription.index')
            ->with('info', 'Pembayaran dibatalkan. Anda boleh cuba lagi bila-bila masa.');
    }

    /**
     * Guard: pastikan package sepadan dengan user (gender, path, poligami).
     */
    private function guardPackageForUser($user, SubscriptionPackage $pkg): void
    {
        $gender = $user->gender ?? null;
        $path = $user->path ?? null;

        if ($pkg->gender !== 'any' && $gender && $pkg->gender !== $gender) {
            abort(403, 'Package tidak sepadan dengan jantina anda.');
        }
        if ($pkg->path && $path && $pkg->path !== $path) {
            abort(403, 'Package tidak sepadan dengan laluan (path) anda.');
        }
        $poligamiSituation = $user->poligami_situation ?? null;
        if (($user->gender ?? null) === 'male' && ($user->path ?? null) === 'poligami') {
            if ($pkg->poligami_situation && $poligamiSituation && (int) $pkg->poligami_situation !== (int) $poligamiSituation) {
                abort(403, 'Package poligami tidak sepadan dengan Default anda.');
            }
        }
        $poligamiLevel = $user->poligami_level ?? null;
        if (($user->gender ?? null) === 'female' && in_array(($user->path ?? null), ['wanita_poligami', 'wanita_terbuka'], true)) {
            if (!$poligamiLevel) {
                abort(403, 'Sila pilih Tahap poligami terlebih dahulu.');
            }
            if ($pkg->poligami_level && (int) $pkg->poligami_level !== (int) $poligamiLevel) {
                abort(403, 'Package tidak sepadan dengan Tahap anda.');
            }
        }
    }

    /**
     * Aktifkan subscription untuk user (create record). Tidak hantar email.
     */
    private function activateSubscriptionForUser($user, SubscriptionPackage $pkg): void
    {
        $amount = (int) $pkg->price_sen;
        $startedAt = now();
        $endsAt = $pkg->duration_days ? now()->addDays((int) $pkg->duration_days) : null;

        DB::transaction(function () use ($user, $pkg, $amount, $startedAt, $endsAt) {
            $referral = AffiliateReferral::query()
                ->where('referred_user_id', $user->id)
                ->first();

            // Commission hanya sekali seumur hidup untuk referred user (first paid subscription)
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
                    // LITE/none tidak layak affiliate → jangan create commission
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
                'amount_sen' => $amount,
                'affiliate_code_used' => $affiliateCodeUsed,
                'referrer_user_id' => $referrerId,
                'commission_percent' => $referrerId ? 0 : null, // fixed amount (bukan %)
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
                    'commission_percent' => 0, // fixed
                    'commission_sen' => (int) $commissionSen,
                    'status' => AffiliateCommission::STATUS_PENDING,
                    'created_at' => now(),
                ]);
            }
            // Early Bird: pakej HYPE → status HYPE; lain: GRADUATE → HYPE (resubscribe), lain → ACTIVE
            $newStatus = ($pkg->code ?? '') === EarlyBirdService::HYPE_CODE
                ? User::STATUS_KEAHLIAN_HYPE
                : (($user->status_keahlian ?? '') === User::STATUS_KEAHLIAN_GRADUATE
                    ? User::STATUS_KEAHLIAN_HYPE
                    : User::STATUS_KEAHLIAN_ACTIVE);
            $user->update(['status_keahlian' => $newStatus]);
        });

        if (($pkg->code ?? '') === EarlyBirdService::HYPE_CODE) {
            (new EarlyBirdService())->maybeDisableEarlyBirdIfCapReached();
        }
    }

    /**
     * Subscribe one-off terus active (no affiliate input)
     */
    public function subscribe(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'package_id' => ['required', 'integer', 'exists:subscription_packages,id'],
        ]);

        $pkg = SubscriptionPackage::where('id', $request->package_id)
            ->where('is_active', 1)
            ->firstOrFail();

        $earlyBird = new EarlyBirdService();
        if ($earlyBird->shouldHideHypePackage() && ($pkg->code ?? '') === EarlyBirdService::HYPE_CODE) {
            return back()->withErrors(['package_id' => 'Pakej Early Bird tidak tersedia pada masa ini.']);
        }

        $this->guardPackageForUser($user, $pkg);

        $currentActive = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        if ($currentActive && $currentActive->ends_at && now()->lt($currentActive->ends_at)) {
            return back()->withErrors([
                'package_id' => 'Anda masih mempunyai subscription aktif sehingga ' . $currentActive->ends_at->format('d/m/Y H:i') . '.',
            ]);
        }

        $this->activateSubscriptionForUser($user, $pkg);

        $emailSent = $this->sendSubscriptionActivatedEmail($user, $pkg);

        return redirect()
            ->route('subscription.ebook.links')
            ->with(
                $emailSent ? 'success' : 'warning',
                $emailSent
                    ? 'Subscription berjaya diaktifkan. Email ebook telah dihantar.'
                    : 'Subscription berjaya diaktifkan, tetapi email tidak berjaya dihantar. Link ebook ada di halaman ini.'
            );
    }

    /**
     * Halaman fallback link ebook (jika email masuk spam / tak diterima)
     */
    public function ebookLinks(Request $request)
    {
        $user = Auth::user();

        $sub = Subscription::with('package')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        if (!$sub || ($sub->ends_at && now()->gte($sub->ends_at))) {
            return redirect()->route('subscription.index')->withErrors([
                'subscription' => 'Tiada subscription aktif untuk akses link ebook.',
            ]);
        }

        return view('subscription.ebook-links', [
            'content' => $this->resolveEbookContent($sub->package->path ?? $user->path),
            'activeSub' => $sub,
        ]);
    }

    private function resolveEbookContent(?string $path): array
    {
        $isPoligami = in_array($path, ['poligami', 'wanita_poligami', 'wanita_terbuka'], true);

        if ($isPoligami) {
            return [
                'link' => self::POLIGAMI_EBOOK_LINK,
                'paragraphs' => [
                    'Sila klik link di bawah untuk dapatkan e-book panduan rumahtangga yang bersesuaian dengan laluan jodoh anda.',
                ],
            ];
        }

        return [
            'link' => self::MONOGAMI_EBOOK_LINK,
            'paragraphs' => [
                'Assalamualaikum',
                'Bersama ini disertakan file ebook berformat PDF',
                'Moga tuan/puan diberi ketenangan jiwa dan kefahaman bila selesai membaca ebook ini nanti. Saya doakan agar kehidupan tuan/puan sentiasa dalam lindungan dan rahmat Allah SWT.',
                'Sekian, terima kasih.',
                "Tulus ikhlas dari\nJodohMurni",
            ],
        ];
    }

    /**
     * Download ebook: redirect ke Google Drive berdasarkan package (hanya untuk subscription aktif)
     */
    public function downloadEbook(Request $request)
    {
        $user = Auth::user();

        $sub = Subscription::with('package')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->firstOrFail();

        if (!$sub->package) {
            abort(404, 'Ebook tidak tersedia.');
        }

        $path = $sub->package->path ?? $user->path ?? null;
        $content = $this->resolveEbookContent($path);
        $ebookUrl = $content['link'];

        return redirect()->away($ebookUrl);
    }

    private function isPaymentBypassEnabled(): bool
    {
        return (bool) config('subscription.payment_bypass', false);
    }

    /**
     * Langkau gateway: aktifkan subscription + hantar email seperti flow bayar sebenar.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function jsonBypassSubscriptionActivate(User $user, SubscriptionPackage $pkg)
    {
        $this->activateSubscriptionForUser($user, $pkg);
        $user->refresh();

        $emailSent = $this->sendSubscriptionActivatedEmail($user, $pkg);

        session()->flash(
            $emailSent ? 'success' : 'warning',
            $emailSent
                ? 'Subscription berjaya diaktifkan. Email ebook telah dihantar.'
                : 'Subscription berjaya diaktifkan, tetapi email tidak berjaya dihantar. Link ebook ada di halaman ini.'
        );

        Log::info('Subscription payment bypass: subscription activated', [
            'user_id' => $user->id,
            'package_id' => $pkg->id,
        ]);

        return response()->json([
            'success' => true,
            'redirectUrl' => route('subscription.ebook.links'),
            'bypass' => true,
        ]);
    }

    /**
     * @return bool true jika email berjaya dihantar
     */
    private function sendSubscriptionActivatedEmail(User $user, SubscriptionPackage $pkg): bool
    {
        $ebookContent = $this->resolveEbookContent($pkg->path ?? $user->path);

        if (! filled($user->email)) {
            Log::warning('User tiada email untuk hantar subscription activated mail.', [
                'user_id' => $user->id,
                'package_id' => $pkg->id,
            ]);

            return false;
        }

        try {
            Mail::to($user->email)->send(
                new SubscriptionActivatedMail($user, $pkg, $ebookContent)
            );

            return true;
        } catch (\Throwable $e) {
            Log::error('Gagal hantar email subscription activated.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'package_id' => $pkg->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
