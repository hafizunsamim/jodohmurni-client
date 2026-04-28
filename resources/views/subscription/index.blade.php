  <link rel="stylesheet" href="{{ asset('css/subscription.css') }}">
@extends('layouts.app')

@section('content')
<div class="container py-3" style="padding-bottom: 90px;">

  <h3 class="fw-bold mb-2" style="color:rgb(10, 126, 62);">Subscription</h3>

  @if($paymentBypass ?? false)
    <div class="alert alert-warning py-2 small mb-3">
      <strong>Payment bypass aktif.</strong> Klik bayar akan terus aktifkan subscription tanpa PayPal/Toyyibpay.
      Set <code>SUBSCRIPTION_PAYMENT_BYPASS=false</code> dalam <code>.env</code> untuk production.
    </div>
  @endif

  @if($useToyyibpay ?? false)
    @if($toyyibpayConfigured ?? false)
      <div class="alert alert-info py-2 small mb-3">
        <strong>Pembayaran melalui Toyyibpay (FPX / Kad Kredit).</strong> Anda akan dihantar ke laman Toyyibpay untuk membuat bayaran sebelum subscription diaktifkan.
      </div>
    @endif
  @elseif($paypalConfigured ?? false)
    <div class="alert alert-info py-2 small mb-3">
      <strong>Pembayaran melalui PayPal.</strong> Anda akan dihantar ke laman PayPal untuk membuat bayaran sebelum subscription diaktifkan.
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
        Subscription terkini: {{ $activeSub->package->name ?? 'Package' }}
      </div>
      <div class="small">
        Status: <strong>{{ strtoupper($activeSub->status) }}</strong><br>
        Bermula: <strong>{{ $activeSub->started_at ? $activeSub->started_at->format('d/m/Y H:i') : '-' }}</strong><br>
        Tamat: <strong>{{ $activeSub->ends_at ? $activeSub->ends_at->format('d/m/Y H:i') : '-' }}</strong>
      </div>

      <div class="mt-2 d-flex gap-2 flex-wrap">
        <a class="btn btn-sm btn-primary" href="{{ route('subscription.ebook.links') }}">Lihat Link Ebook</a>

        @if(($activeSub->package->ebook_path ?? null))
          <a class="btn btn-sm btn-success" href="{{ route('ebook.download') }}">Muat Turun Ebook</a>
        @endif
      </div>
    </div>
  @endif

  {{-- Nota untuk wanita poligami/terbuka kalau belum set tahap --}}
  @if(($user->gender ?? null) === 'female' && in_array(($user->path ?? null), ['wanita_poligami','wanita_terbuka'], true) && empty($user->poligami_level))
    <div class="alert alert-warning">
      Anda belum pilih <strong>Model Poligami</strong>. Sila set dahulu supaya package yang betul dipaparkan.
    </div>
  @endif

  {{-- Senarai package --}}
  @if($packages->isEmpty())
    <div class="alert alert-secondary">Tiada package dipaparkan untuk akaun anda.</div>
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
                    Tempoh: <strong>{{ ($pkg->code ?? '') === 'HYPE' ? 'Selamanya' : ($pkg->duration_days ? $pkg->duration_days.' hari' : '-') }}</strong>
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
                <button type="button" class="btn btn-secondary w-100" disabled>Subscription Masih Aktif</button>
                <div class="text-muted small mt-2">
                  Anda perlu tunggu subscription tamat sebelum aktifkan package baharu.
                </div>
              @elseif($useToyyibpay ?? false)
                @if($toyyibpayConfigured ?? false)
                  <div class="mb-2">
                    <button type="button" class="btn btn-primary w-100 toyyibpay-btn" data-package-id="{{ $pkg->id }}">
                      {{ $gatewayLabel ?? 'Bayar dengan Toyyibpay (FPX/Kad)' }}
                    </button>
                  </div>
                  <div class="text-muted small mb-2">Pembayaran selamat melalui Toyyibpay (FPX / Kad Kredit).</div>
                @else
                  <div class="alert alert-warning small mb-2">
                    Toyyibpay belum dikonfigurasi. Isi TOYYIBPAY_USER_SECRET_KEY dan TOYYIBPAY_CATEGORY_CODE dalam .env.
                  </div>
                  <form method="POST" action="{{ route('subscription.subscribe') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                    <button class="btn btn-success w-100" type="submit">Aktifkan Subscription (tanpa bayaran)</button>
                  </form>
                @endif
              @elseif($paypalConfigured ?? false)
                <div class="mb-2">
                  <button type="button" class="btn btn-primary w-100 paypal-btn" data-package-id="{{ $pkg->id }}">
                    {{ $gatewayLabel ?? 'Bayar dengan PayPal' }}
                  </button>
                </div>
                <div class="text-muted small mb-2">Pembayaran selamat melalui PayPal.</div>
              @else
                <div class="alert alert-warning small mb-2">
                  PayPal belum dikonfigurasi. Isi PAYPAL_CLIENT_ID dan PAYPAL_SECRET dalam .env.
                </div>
                <form method="POST" action="{{ route('subscription.subscribe') }}">
                  @csrf
                  <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                  <button class="btn btn-success w-100" type="submit">Aktifkan Subscription (tanpa bayaran)</button>
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
  var btns = document.querySelectorAll('.paypal-btn');
  var createOrderUrl = @json(route('subscription.paypal.create'));
  var csrf = document.querySelector('meta[name="csrf-token"]');
  csrf = csrf ? csrf.getAttribute('content') : '';
  var defaultLabel = 'Bayar dengan PayPal';

  btns.forEach(function(btn) {
    if (btn.disabled) return;
    var lbl = btn.textContent;
    btn.addEventListener('click', function() {
      var packageId = this.getAttribute('data-package-id');
      if (!packageId) return;
      this.disabled = true;
      this.textContent = 'Memproses...';

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
        if (data.success && data.redirectUrl) {
          window.location.href = data.redirectUrl;
          return;
        }
        if (data.success && data.approveUrl) {
          window.location.href = data.approveUrl;
          return;
        }
        alert(data.message || 'Gagal cipta pesanan. Sila cuba lagi.');
        btn.disabled = false;
        btn.textContent = lbl || defaultLabel;
      })
      .catch(function() {
        alert('Ralat rangkaian. Sila cuba lagi.');
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
  var btns = document.querySelectorAll('.toyyibpay-btn');
  var createBillUrl = @json(route('subscription.toyyibpay.create'));
  var csrf = document.querySelector('meta[name="csrf-token"]');
  csrf = csrf ? csrf.getAttribute('content') : '';
  var defaultLabel = 'Bayar dengan Toyyibpay (FPX/Kad)';

  btns.forEach(function(btn) {
    var lbl = btn.textContent;
    btn.addEventListener('click', function() {
      var packageId = this.getAttribute('data-package-id');
      if (!packageId) return;
      this.disabled = true;
      this.textContent = 'Memproses...';

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
        if (data.success && data.redirectUrl) {
          window.location.href = data.redirectUrl;
          return;
        }
        if (data.success && data.paymentUrl) {
          window.location.href = data.paymentUrl;
          return;
        }
        alert(data.message || 'Gagal cipta bil. Sila cuba lagi.');
        btn.disabled = false;
        btn.textContent = lbl || defaultLabel;
      })
      .catch(function() {
        alert('Ralat rangkaian. Sila cuba lagi.');
        btn.disabled = false;
        btn.textContent = lbl || defaultLabel;
      });
    });
  });
})();
</script>
@endpush
@endif
@endsection
