@extends('layouts.app')

@section('content')
<div class="onboarding-wrapper">
  <div class="w-100 jm-affiliate-shell">

    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
      <div>
        <h3 class="fw-bold mb-1">Affiliate Dashboard</h3>
        <div class="text-muted">Pantau prestasi affiliate dan komisen anda secara ringkas.</div>
      </div>

      <div class="d-flex flex-wrap gap-2">
        @if(!empty($affiliateLink))
        <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2"
                data-copy-affiliate-link="{{ $affiliateLink }}">
          <span style="font-weight:800;">🔗</span>
          <span>Salin Link Affiliate</span>
        </button>
        @endif

        <button type="button" class="btn btn-outline-success d-inline-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#externalAffiliatePasswordModal">
          <span style="font-weight:800;">🔒</span>
          <span>Tukar Password</span>
        </button>
      </div>
    </div>

    <div class="jm-affiliate-panel animate-in mb-3">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div style="min-width: 220px;">
          <div class="text-muted small">Jumlah Komisen Terkumpul</div>
          <div class="fw-bold" style="font-size: 34px; line-height: 1.1; color:#1f2937;">
            RM {{ number_format(((int)$totalSen) / 100, 2) }}
          </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
          <div class="p-3 rounded-4 bg-white border" style="min-width: 170px;">
            <div class="small text-muted">Pending Payment</div>
            <div class="fw-bold">RM {{ number_format(((int)$pendingSen) / 100, 2) }}</div>
          </div>
          <div class="p-3 rounded-4 bg-white border" style="min-width: 170px;">
            <div class="small text-muted">Paid Out</div>
            <div class="fw-bold">RM {{ number_format(((int)$paidSen) / 100, 2) }}</div>
          </div>
          <div class="p-3 rounded-4 bg-white border" style="min-width: 170px;">
            <div class="small text-muted">Rejected</div>
            <div class="fw-bold">RM {{ number_format(((int)$rejectedSen) / 100, 2) }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="jm-affiliate-panel animate-in">
          <div class="text-muted small">Bilangan Referral Berjaya</div>
          <div class="fw-bold" style="font-size: 28px;">{{ (int) $successfulSubscribers }}</div>
          <div class="text-muted small">Jumlah user yang subscribe menggunakan link affiliate anda.</div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-6">
        <div class="jm-affiliate-panel animate-in">
          <div class="text-muted small">Jumlah Klik Link Affiliate</div>
          <div class="fw-bold" style="font-size: 28px;">{{ (int) $affiliateClicks }}</div>
          <div class="text-muted small">Dikira berdasarkan klik pada link affiliate.</div>
        </div>
      </div>

      <!-- <div class="col-12 col-lg-4">
        <div class="jm-affiliate-panel animate-in">
          <div class="text-muted small">Link Affiliate</div>
          @if(!empty($affiliateLink))
            <div class="fw-semibold" style="word-break: break-all;">{{ $affiliateLink }}</div>
            <div class="mt-2">
              <button type="button" class="btn btn-outline-success btn-sm"
                      data-copy-affiliate-link="{{ $affiliateLink }}">Salin</button>
            </div>
          @else
            <div class="text-muted">Link affiliate belum tersedia.</div>
          @endif
        </div>
      </div> -->
    </div>

  </div>
</div>

{{-- Modal: Update Password --}}
<div class="modal fade" id="externalAffiliatePasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <div class="fw-bold">Tukar Password</div>
          <div class="text-muted small">Pastikan password baru anda selamat.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('affiliate.external.password.update') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Password baru</label>
            <div class="input-group">
              <input class="form-control" type="password" name="password" id="ext_new_password" minlength="8" maxlength="72" required>
              <button class="btn btn-outline-secondary" type="button" data-toggle-password="#ext_new_password" aria-label="Toggle password">
                👁
              </button>
            </div>
            @error('password')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Sahkan password baru</label>
            <div class="input-group">
              <input class="form-control" type="password" name="password_confirmation" id="ext_new_password_confirm" minlength="8" maxlength="72" required>
              <button class="btn btn-outline-secondary" type="button" data-toggle-password="#ext_new_password_confirm" aria-label="Toggle password confirmation">
                👁
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-between gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            <button class="btn btn-success" type="submit">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {
  function copyText(text) {
    if (!text) return;
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(text);
    }
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    try { document.execCommand('copy'); } catch (e) {}
    document.body.removeChild(ta);
    return Promise.resolve();
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-copy-affiliate-link]');
    if (!btn) return;
    var link = btn.getAttribute('data-copy-affiliate-link');
    copyText(link).then(function () {
      btn.classList.add('disabled');
      var old = btn.innerText;
      btn.innerText = 'Disalin';
      setTimeout(function () {
        btn.classList.remove('disabled');
        btn.innerText = old;
      }, 1200);
    });
  });

  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-toggle-password]');
    if (!t) return;
    var sel = t.getAttribute('data-toggle-password');
    var input = sel ? document.querySelector(sel) : null;
    if (!input) return;
    input.type = (input.type === 'password') ? 'text' : 'password';
  });
})();
</script>
@endpush
@endsection

