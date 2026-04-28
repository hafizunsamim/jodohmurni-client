@extends('layouts.app')

@section('content')
<div class="onboarding-wrapper">
  <div class="w-100 jm-affiliate-shell">

    <div class="text-center mb-3">
      <h3 class="fw-bold mb-1">Affiliate</h3>
      <div class="text-muted">Mohon jadi Affiliate Pro tanpa perlu ada akaun.</div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 g-lg-4 align-items-stretch">
      {{-- Left: Benefits --}}
      <div class="col-12 col-lg-6">
        <div class="jm-affiliate-panel jm-affiliate-panel--left animate-in">
          <div class="jm-affiliate-panel__title">Kenapa jadi affiliate?</div>

          <ul class="jm-affiliate-list">
            <li><span class="jm-affiliate-list__icon">✓</span> Dapat komisen bagi setiap pendaftaran berjaya</li>
            <li><span class="jm-affiliate-list__icon">✓</span> Affiliate Pro dapat komisen <strong>RM10</strong></li>
            <li><span class="jm-affiliate-list__icon">✓</span> Boleh jana pendapatan sampingan dengan berkongsi link affiliate</li>
            <li><span class="jm-affiliate-list__icon">✓</span> Mudah untuk bermula</li>
          </ul>

          <div class="jm-affiliate-commission">
            <div class="jm-affiliate-commission__label">Kadar Komisen</div>
            <div class="jm-affiliate-commission__amount">RM 10.00</div>
            <div class="jm-affiliate-commission__hint">untuk Affiliate Pro</div>
          </div>
        </div>
      </div>

      {{-- Right: Form --}}
      <div class="col-12 col-lg-6">
        <div class="jm-affiliate-panel jm-affiliate-panel--right animate-in">
          <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
            <div class="jm-affiliate-panel__title mb-0">Borang Permohonan</div>
            <!-- <div class="text-muted small text-end">Semua medan bertanda <span class="text-danger">*</span> adalah wajib.</div> -->
          </div>

          <form method="POST" action="{{ route('affiliate.public.apply') }}" class="mt-3">
            @csrf

            <div class="form-floating mb-3">
              <input type="text" name="full_name" class="form-control" id="aff_full_name" placeholder="Nama penuh"
                     value="{{ old('full_name') }}" maxlength="190" required>
              <label for="aff_full_name">Nama penuh <span class="text-danger">*</span></label>
            </div>

            <div class="form-floating mb-3">
              <input type="email" name="email" class="form-control" id="aff_email" placeholder="nama@email.com"
                     value="{{ old('email') }}" maxlength="190" required>
              <label for="aff_email">Emel <span class="text-danger">*</span></label>
            </div>

            <div class="form-floating mb-2">
              <input type="text" name="phone_number" class="form-control" id="aff_phone" placeholder="+60123456789"
                     value="{{ old('phone_number') }}" maxlength="50" required>
              <label for="aff_phone">Nombor telefon <span class="text-danger">*</span></label>
            </div>
            <div class="text-muted small mb-3">Contoh: +60123456789</div>

            <div class="form-floating mb-3">
              <input type="text" name="promotion_platform" class="form-control" id="aff_platform" placeholder="Platform promosi"
                     value="{{ old('promotion_platform') }}" maxlength="120" required>
              <label for="aff_platform">Platform promosi <span class="text-danger">*</span></label>
            </div>

            <div class="mb-3">
              <label class="form-label">Sebab permohonan <span class="text-danger">*</span></label>
              <textarea class="form-control" name="reason" rows="6" minlength="10" maxlength="2000" required
                        placeholder="Tulis sebab anda ingin menjadi Affiliate Pro...">{{ old('reason') }}</textarea>
            </div>

            <button class="btn btn-success w-100 py-2" type="submit">Hantar Permohonan</button>
          </form>

          <div class="text-center mt-3">
            <button type="button" class="btn btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#externalAffiliateLoginModal">
              Tekan sini jika telah berdaftar sebagai Affiliate Pro
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Modal Login --}}
<div class="modal fade" id="externalAffiliateLoginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <div class="fw-bold">Login Affiliate Pro</div>
          <div class="text-muted small">Untuk affiliate yang diluluskan melalui permohonan public.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('affiliate.external.login') }}">
          @csrf
          <div class="form-floating mb-3">
            <input class="form-control" type="email" name="email" id="ext_aff_email" placeholder="nama@email.com" value="{{ old('email') }}" required>
            <label for="ext_aff_email">Emel</label>
          </div>
          <div class="form-floating mb-3">
            <input class="form-control" type="password" name="password" id="ext_aff_password" placeholder="Password" required>
            <label for="ext_aff_password">Kata laluan</label>
          </div>
          <div class="d-flex justify-content-end">
            <button class="btn btn-primary" type="submit">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

