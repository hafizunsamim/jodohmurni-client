  <link rel="stylesheet" href="{{ asset('css/subscription.css') }}">
@extends('layouts.app')

@section('content')
<div class="container py-3" style="padding-bottom: 90px;">

  <h3 class="fw-bold mb-2" style="color:rgb(10, 126, 62);" data-translate="sub_title">Subscription</h3>

  @if($paymentBypass ?? false)
    <div class="alert alert-warning py-2 small mb-3">
      <strong data-translate="sub_payment_bypass_on">Payment bypass aktif.</strong>
      <span data-translate="sub_payment_bypass_desc">Klik bayar akan terus aktifkan subscription tanpa PayPal/Toyyibpay.</span>
      <span data-translate="sub_payment_bypass_note">Set SUBSCRIPTION_PAYMENT_BYPASS=false dalam .env untuk production.</span>
    </div>
  @endif

  @if($useToyyibpay ?? false)
    @if($toyyibpayConfigured ?? false)
      <div class="alert alert-info py-2 small mb-3">
        <strong data-translate="sub_pay_toyyibpay_title">Pembayaran melalui Toyyibpay (FPX / Kad Kredit).</strong>
        <span data-translate="sub_pay_toyyibpay_desc">Anda akan dihantar ke laman Toyyibpay untuk membuat bayaran sebelum subscription diaktifkan.</span>
      </div>
    @endif
  @elseif($paypalConfigured ?? false)
    <div class="alert alert-info py-2 small mb-3">
      <strong data-translate="sub_pay_paypal_title">Pembayaran melalui PayPal.</strong>
      <span data-translate="sub_pay_paypal_desc">Anda akan dihantar ke laman PayPal untuk membuat bayaran sebelum subscription diaktifkan.</span>
    </div>
  @endif

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
  @endif

  @if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Info subscription aktif --}}
  @if(isset($activeSub) && $activeSub)
    <div class="alert {{ ($stillActive ?? false) ? 'alert-info' : 'alert-secondary' }}">
      <div class="fw-bold mb-1">
        <span data-translate="sub_current_subscription">Subscription terkini</span>: {{ $activeSub->package->name ?? 'Package' }}
      </div>
      <div class="small">
        <span data-translate="sub_status">Status</span>: <strong>{{ strtoupper($activeSub->status) }}</strong><br>
        <span data-translate="sub_started_at">Bermula</span>: <strong>{{ $activeSub->started_at ? $activeSub->started_at->format('d/m/Y H:i') : '-' }}</strong><br>
        <span data-translate="sub_ends_at">Tamat</span>: <strong>{{ $activeSub->ends_at ? $activeSub->ends_at->format('d/m/Y H:i') : '-' }}</strong>
      </div>

      <div class="mt-2 d-flex gap-2 flex-wrap">
        <a class="btn btn-sm btn-primary" href="{{ route('subscription.ebook.links') }}" data-translate="sub_view_ebook_links">Lihat Link Ebook</a>

        @if(($activeSub->package->ebook_path ?? null))
          <a class="btn btn-sm btn-success" href="{{ route('ebook.download') }}" data-translate="sub_download_ebook">Muat Turun Ebook</a>
        @endif
      </div>
    </div>
  @endif

  {{-- Nota untuk wanita poligami/terbuka kalau belum set tahap --}}
  @if(($user->gender ?? null) === 'female' && in_array(($user->path ?? null), ['wanita_poligami','wanita_terbuka'], true) && empty($user->poligami_level))
    <div class="alert alert-warning">
      <span data-translate="sub_poligami_note_1">Anda belum pilih</span> <strong data-translate="sub_poligami_model">Model Poligami</strong>.
      <span data-translate="sub_poligami_note_2">Sila set dahulu supaya package yang betul dipaparkan.</span>
    </div>
  @endif

  {{-- Senarai package --}}
  @if($packages->isEmpty())
    <div class="alert alert-secondary" data-translate="sub_no_packages">Tiada package dipaparkan untuk akaun anda.</div>
  @else
    <div class="row g-3">
      @foreach($packages as $pkg)
        <div class="col-12 col-md-6">
          <div class="card h-100 shadow-sm card-soft-green">
            <div class="card-body">

              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h5 class="mb-1">{{ $pkg->name }}</h5>
                  <div class="text-muted small">
                    <span data-translate="sub_duration">Tempoh</span>:
                    <strong>
                      @if(($pkg->code ?? '') === 'HYPE')
                        <span data-translate="sub_forever">Selamanya</span>
                      @elseif($pkg->duration_days)
                        {{ $pkg->duration_days }} <span data-translate="sub_days">hari</span>
                      @else
                        -
                      @endif
                    </strong>
                  </div>
                </div>

                <div class="text-end">
                  <div class="fw-bold" style="font-size:18px;">
                    RM {{ number_format($pkg->price_sen / 100, 2) }}
                  </div>
                  <div class="text-muted small">{{ $pkg->currency }}</div>
                </div>
              </div>

              <hr>

              @if(($stillActive ?? false))
                <button type="button" class="btn btn-secondary w-100" disabled data-translate="sub_still_active_btn">Subscription Masih Aktif</button>
                <div class="text-muted small mt-2">
                  <span data-translate="sub_still_active_note">Anda perlu tunggu subscription tamat sebelum aktifkan package baharu.</span>
                </div>
              @elseif($useToyyibpay ?? false)
                @if($toyyibpayConfigured ?? false)
                  <div class="mb-2">
                    <button type="button" class="btn btn-primary w-100 toyyibpay-btn" data-package-id="{{ $pkg->id }}" data-package-name="{{ e($pkg->name) }}" data-package-code="{{ e($pkg->code ?? '') }}">
                      <span data-translate="sub_pay_with_toyyibpay">{{ $gatewayLabel ?? 'Bayar dengan Toyyibpay (FPX/Kad)' }}</span>
                    </button>
                  </div>
                  <div class="text-muted small mb-2" data-translate="sub_toyyibpay_safe">Pembayaran selamat melalui Toyyibpay (FPX / Kad Kredit).</div>
                @else
                  <div class="alert alert-warning small mb-2">
                    <span data-translate="sub_toyyibpay_not_configured">Toyyibpay belum dikonfigurasi. Isi TOYYIBPAY_USER_SECRET_KEY dan TOYYIBPAY_CATEGORY_CODE dalam .env.</span>
                  </div>
                  <form method="POST" action="{{ route('subscription.subscribe') }}" class="jm-subscribe-form" data-package-name="{{ e($pkg->name) }}" data-package-code="{{ e($pkg->code ?? '') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                    <button class="btn btn-success w-100" type="submit" data-translate="sub_activate_no_payment">Aktifkan Subscription (tanpa bayaran)</button>
                  </form>
                @endif
              @elseif($paypalConfigured ?? false)
                <div class="mb-2">
                  <button type="button" class="btn btn-primary w-100 paypal-btn" data-package-id="{{ $pkg->id }}" data-package-name="{{ e($pkg->name) }}" data-package-code="{{ e($pkg->code ?? '') }}">
                    <span data-translate="sub_pay_with_paypal">{{ $gatewayLabel ?? 'Bayar dengan PayPal' }}</span>
                  </button>
                </div>
                <div class="text-muted small mb-2" data-translate="sub_paypal_safe">Pembayaran selamat melalui PayPal.</div>
              @else
                <div class="alert alert-warning small mb-2">
                  <span data-translate="sub_paypal_not_configured">PayPal belum dikonfigurasi. Isi PAYPAL_CLIENT_ID dan PAYPAL_SECRET dalam .env.</span>
                </div>
                <form method="POST" action="{{ route('subscription.subscribe') }}" class="jm-subscribe-form" data-package-name="{{ e($pkg->name) }}" data-package-code="{{ e($pkg->code ?? '') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                    <button class="btn btn-success w-100" type="submit" data-translate="sub_activate_no_payment">Aktifkan Subscription (tanpa bayaran)</button>
                  </form>
              @endif

            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif

</div>

@include('partials.footer-nav', ['active' => ''])

@if(($paymentBypass ?? false) || (($paypalConfigured ?? false) && ($usePayPal ?? true)))
@push('scripts')
<script>
(function() {
  function jmT(key, fallback) {
    try {
      var translations = (window.JM_TRANSLATIONS && typeof window.JM_TRANSLATIONS === "object") ? window.JM_TRANSLATIONS : {};
      var country = ((document.body && document.body.dataset && document.body.dataset.country) ? document.body.dataset.country : "MY").trim() || "MY";
      var locale = ((document.body && document.body.dataset && document.body.dataset.locale) ? document.body.dataset.locale : "").trim() || "ms";
      var byCountry = translations[country] || translations["MY"] || {};
      var pack = byCountry[locale] || byCountry["en"] || {};
      return pack[key] || (byCountry["en"] ? byCountry["en"][key] : null) || fallback || key;
    } catch (e) {
      return fallback || key;
    }
  }

  function jmFireGa4EventsFromJson(data) {
    if (!data || !Array.isArray(data.analytics_events) || typeof gtag !== 'function') return;
    data.analytics_events.forEach(function (ev) {
      if (ev && ev.name) gtag('event', ev.name, ev.params || {});
    });
  }
  var btns = document.querySelectorAll('.paypal-btn');
  var createOrderUrl = @json(route('subscription.paypal.create'));
  var csrf = document.querySelector('meta[name="csrf-token"]');
  csrf = csrf ? csrf.getAttribute('content') : '';
  var defaultLabel = jmT('sub_pay_with_paypal_plain', 'Pay with PayPal');

  btns.forEach(function(btn) {
    if (btn.disabled) return;
    var lbl = btn.textContent;
    btn.addEventListener('click', function() {
      var packageId = this.getAttribute('data-package-id');
      if (!packageId) return;
      if (typeof gtag === 'function') {
        gtag('event', 'subscription_package_click', {
          package_id: packageId,
          package_name: (this.getAttribute('data-package-name') || '').slice(0, 100),
          package_code: (this.getAttribute('data-package-code') || '').slice(0, 32),
          payment_method: 'paypal'
        });
      }
      this.disabled = true;
      this.textContent = jmT('sub_processing', 'Processing...');

      var formData = new FormData();
      formData.append('package_id', packageId);
      formData.append('_token', csrf);

      fetch(createOrderUrl, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        credentials: 'same-origin'
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        jmFireGa4EventsFromJson(data);
        if (data.success && data.redirectUrl) {
          window.location.href = data.redirectUrl;
          return;
        }
        if (data.success && data.approveUrl) {
          window.location.href = data.approveUrl;
          return;
        }
        alert(data.message || jmT('sub_order_create_failed', 'Failed to create order. Please try again.'));
        btn.disabled = false;
        btn.textContent = lbl || defaultLabel;
      })
      .catch(function() {
        alert(jmT('sub_network_error_try_again', 'Network error. Please try again.'));
        btn.disabled = false;
        btn.textContent = lbl || defaultLabel;
      });
    });
  });
})();
</script>
@endpush
@endif

@if(($paymentBypass ?? false) || (($toyyibpayConfigured ?? false) && ($useToyyibpay ?? false)))
@push('scripts')
<script>
(function() {
  function jmT(key, fallback) {
    try {
      var translations = (window.JM_TRANSLATIONS && typeof window.JM_TRANSLATIONS === "object") ? window.JM_TRANSLATIONS : {};
      var country = ((document.body && document.body.dataset && document.body.dataset.country) ? document.body.dataset.country : "MY").trim() || "MY";
      var locale = ((document.body && document.body.dataset && document.body.dataset.locale) ? document.body.dataset.locale : "").trim() || "ms";
      var byCountry = translations[country] || translations["MY"] || {};
      var pack = byCountry[locale] || byCountry["en"] || {};
      return pack[key] || (byCountry["en"] ? byCountry["en"][key] : null) || fallback || key;
    } catch (e) {
      return fallback || key;
    }
  }

  function jmFireGa4EventsFromJson(data) {
    if (!data || !Array.isArray(data.analytics_events) || typeof gtag !== 'function') return;
    data.analytics_events.forEach(function (ev) {
      if (ev && ev.name) gtag('event', ev.name, ev.params || {});
    });
  }
  var btns = document.querySelectorAll('.toyyibpay-btn');
  var createBillUrl = @json(route('subscription.toyyibpay.create'));
  var csrf = document.querySelector('meta[name="csrf-token"]');
  csrf = csrf ? csrf.getAttribute('content') : '';
  var defaultLabel = jmT('sub_pay_with_toyyibpay_plain', 'Pay with Toyyibpay (FPX/Card)');

  btns.forEach(function(btn) {
    var lbl = btn.textContent;
    btn.addEventListener('click', function() {
      var packageId = this.getAttribute('data-package-id');
      if (!packageId) return;
      if (typeof gtag === 'function') {
        gtag('event', 'subscription_package_click', {
          package_id: packageId,
          package_name: (this.getAttribute('data-package-name') || '').slice(0, 100),
          package_code: (this.getAttribute('data-package-code') || '').slice(0, 32),
          payment_method: 'toyyibpay'
        });
      }
      this.disabled = true;
      this.textContent = jmT('sub_processing', 'Processing...');

      var formData = new FormData();
      formData.append('package_id', packageId);
      formData.append('_token', csrf);

      fetch(createBillUrl, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        credentials: 'same-origin'
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        jmFireGa4EventsFromJson(data);
        if (data.success && data.redirectUrl) {
          window.location.href = data.redirectUrl;
          return;
        }
        if (data.success && data.paymentUrl) {
          window.location.href = data.paymentUrl;
          return;
        }
        alert(data.message || jmT('sub_bill_create_failed', 'Failed to create bill. Please try again.'));
        btn.disabled = false;
        btn.textContent = lbl || defaultLabel;
      })
      .catch(function() {
        alert(jmT('sub_network_error_try_again', 'Network error. Please try again.'));
        btn.disabled = false;
        btn.textContent = lbl || defaultLabel;
      });
    });
  });
})();
</script>
@endpush
@endif

@if(config('analytics.google_measurement_id'))
@push('scripts')
<script>
(function () {
  document.querySelectorAll('form.jm-subscribe-form').forEach(function (form) {
    form.addEventListener('submit', function () {
      if (typeof gtag !== 'function') return;
      var pidInput = form.querySelector('input[name="package_id"]');
      var packageId = pidInput ? pidInput.value : '';
      gtag('event', 'subscription_package_click', {
        package_id: packageId,
        package_name: (form.getAttribute('data-package-name') || '').slice(0, 100),
        package_code: (form.getAttribute('data-package-code') || '').slice(0, 32),
        payment_method: 'direct_activate'
      });
    });
  });
})();
</script>
@endpush
@endif
@endsection
