  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">

@extends('layouts.app')

@section('content')
<section class="section-main mb-4">
@php
    // array_filter kekalkan index asal; reset index supaya boleh akses [0] dengan selamat
    $photos = array_values(array_filter([
        $user->photo_1,
        $user->photo_2,
        $user->photo_3,
        $user->photo_4,
    ]));
    $mainPhoto = $photos ? asset($photos[0]) : asset('images/default.jpg');
    $candidateAge = $user->date_of_birth
        ? \Carbon\Carbon::parse($user->date_of_birth)->age
        : null;

    // ✅ ikut domain semasa (localhost / subdomain / cloudflare tunnel)
    $baseUrl = request()->getSchemeAndHttpHost();

    $affiliateUrl = isset($affiliate)
        ? ($baseUrl . '/?ref=' . $affiliate->code)
        : $baseUrl;
@endphp

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <img class="single-d-main-img" src="{{ $mainPhoto }}" alt="{{ e($user->name) }}">

    <h2 class="single-profile-nam">{{ $user->name }}</h2>

    <div class="romio-girl-data-main single-isabell">
        @if($candidateAge)
            <p><img src="{{ asset('assets/images/svg/gender-icon2.svg') }}" alt="age"> {{ $candidateAge }} yr</p>
        @endif
        <p><img src="{{ asset('assets/images/svg/home-flo.svg') }}" alt="country"> {{ ucfirst($user->country ?? '-') }}</p>
        @if($user->latitude && $user->longitude)
            <p><img src="{{ asset('assets/images/svg/location-home.svg') }}" alt="location"> Lokasi anda</p>
        @endif
    </div>

    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm mb-3">
  Edit Profile
</a>

    <!-- Interests -->
    @php
        $hobbies = $user->hobbies ? explode(',', $user->hobbies) : [];
        $social = $user->social_activities ? explode(',', $user->social_activities) : [];
        $interests = array_unique(array_merge($hobbies, $social));
    @endphp

    @if(!empty($interests))
    <div class="about-box">
        <p class="issAbout-me">Minat</p>
        <div class="inte-mian">
            @foreach($interests as $i => $interest)
                @if($i < 5)
                    <p class="int-box">{{ trim($interest) }}</p>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Photos -->
    @if(!empty($photos))
    <div class="about-box">
        <p class="issAbout-me">Gambar</p>
        <div class="masonry-grid">
            @foreach($photos as $photo)
                <img src="{{ asset($photo) }}" alt="Gambar profil">
            @endforeach
        </div>
    </div>
    @endif

    <!-- =========================
         SUBSCRIPTION
         ========================= -->
    <div class="about-box">
        <p class="issAbout-me">Langganan</p>

        @if($activeSub)
            <div class="p-3 border rounded-3 bg-white">
                <div class="fw-bold">Status: Aktif</div>
                <div class="text-muted small">
                    Package: {{ isset($activePkg) && $activePkg ? ($activePkg->name ?? '—') : '—' }}<br>
                    Dibayar: RM {{ number_format($activeSub->amount_sen ?? 0) }}
                </div>

                <!-- <hr>

                <div class="fw-bold">Link Affiliate (komisen RM5 / RM10)</div>
                <div class="text-muted small mb-2">
                    Share link ini. Bila orang daftar akaun guna link ini dan kemudian subscribe, anda dapat komisen (RM5 jika anda LITE, RM10 jika anda pernah subscribe).
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <input class="form-control" value="{{ $affiliateUrl }}" readonly style="max-width:520px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                    onclick="copyLink('{{ $affiliateUrl }}')"> 
                Copy
                    </button>
            
                </div> -->

                <div class="mt-3">
                    <a class="btn btn-warning btn-sm" href="{{ route('ebook.download') }}">
                        Download E-book
                    </a>
                    <div class="text-muted small mt-1">
                        *Link e-book juga dihantar melalui email selepas langganan aktif.
                    </div>
                </div>
            </div>
        @else
<div class="p-3 border rounded-3 bg-white">
    <div class="fw-bold">Belum Langgan</div>

    <div class="text-muted small mb-3">
        Untuk buka <b>nama penuh</b>, <b>nickname</b>, dan <b>chat tanpa batas</b>,
        sila aktifkan subscription anda.
        <br><br>
        Klik butang di bawah untuk lihat senarai package dan harga.
    </div>

    <a href="{{ route('subscription.index') }}" class="btn btn-success w-100">
        Lihat & Aktifkan Subscription
    </a>

    <div class="text-muted small mt-2">
        *Selepas subscription aktif, semua butiran calon akan dipaparkan secara penuh.
    </div>
    
</div>

        @endif
    </div>

    <!-- =========================
         AFFILIATE (HIDE UNTUK LITE)
         ========================= -->
    @if(($affiliateProfile['enabled'] ?? false) && isset($affiliate))
    <div class="about-box">
        <p class="issAbout-me">Affiliate</p>

        <div class="p-3 border rounded-3 bg-white">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <div>
                    <div class="fw-bold">Link Affiliate</div>
                    <div class="text-muted small">
                        Kongsi link ini. Komisen hanya direkod bila user daftar akaun melalui link ini dan kemudian subscribe.
                    </div>
                </div>
                <div class="text-end">
                    @if(($affiliateProfile['tier'] ?? '') === 'pro')
                        <span class="badge bg-success">Affiliate Pro</span>
                    @elseif(($affiliateProfile['tier'] ?? '') === 'standard')
                        <span class="badge bg-secondary">Affiliate Standard</span>
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <div class="text-muted small mb-1">Kadar komisen semasa</div>
                <div class="fw-bold">{{ $affiliateProfile['commission_label'] ?? 'RM 0.00' }}</div>
            </div>

            <div class="d-flex gap-2 flex-wrap mt-3">
                <input class="form-control" value="{{ $affiliateUrl }}" readonly style="max-width:520px;">
                <button type="button" class="btn btn-outline-secondary btn-sm"
                        onclick="copyLink('{{ $affiliateUrl }}')">
                    Copy
                </button>
            </div>

            {{-- Upgrade / Status button (ACTIVE/GRADUATE standard sahaja) --}}
            @php
                $latestStatus = strtolower((string)($latestProReq->status ?? ''));
                $canApply = (bool)($affiliateProfile['can_apply_pro'] ?? false);
                $isStandard = (($affiliateProfile['tier'] ?? '') === 'standard');
                $isPending = ($latestStatus === 'pending');
                $isRejected = ($latestStatus === 'rejected');
            @endphp

            @if($isStandard)
                <div class="mt-3">
                    @if($isPending)
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#affiliateProModal">
                            Lihat Status Permohonan
                        </button>
                    @elseif($isRejected)
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#affiliateProModal">
                            Mohon Semula Affiliate Pro
                        </button>
                    @elseif($canApply)
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#affiliateProModal">
                            Naik Taraf ke Affiliate Pro
                        </button>
                    @else
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#affiliateProModal">
                            Lihat Status Permohonan
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Modal: Apply / Status / History --}}
    <div class="modal fade" id="affiliateProModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Affiliate Pro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">

            @php
              $history = $proReqHistory ?? collect();
            @endphp

            @if($history->isNotEmpty())
              <div class="mb-3">
                <div class="d-flex align-items-center gap-2 fw-bold mb-2">
                  <span style="font-size:18px;line-height:1;">🕒</span>
                  <span>Sejarah Permohonan</span>
                </div>

                <div class="p-3 rounded-4" style="border:2px dashed #b48ad6; background:#fbf7ff;">
                  @foreach($history as $r)
                    @php
                      $st = strtolower((string)$r->status);
                    @endphp
                    @php
                      $badgeClass = $st === 'approved' ? 'bg-success'
                        : ($st === 'rejected' ? 'bg-danger' : 'bg-warning');
                      $badgeLabel = $st === 'approved' ? 'Approved'
                        : ($st === 'rejected' ? 'Rejected' : 'Pending');
                      $iconBg = $st === 'approved' ? '#2e7d32' : ($st === 'rejected' ? '#d32f2f' : '#1976d2');
                      $icon = $st === 'approved' ? '✓' : ($st === 'rejected' ? '✕' : '🕒');
                    @endphp

                    <div class="bg-white border rounded-4 p-3 mb-3" style="box-shadow: 0 1px 2px rgba(0,0,0,.05);">
                      <div class="d-flex gap-3 align-items-start">
                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                             style="width:44px;height:44px;background:{{ $iconBg }};color:#fff;font-weight:700;">
                          {{ $icon }}
                        </div>

                        <div class="flex-grow-1">
                          <div class="d-flex justify-content-between gap-2">
                            <div class="fw-semibold">
                              {{ $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-' }}
                            </div>
                            <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                          </div>

                          <div class="text-muted small">Reason: {{ $r->reason }}</div>

                          @if($st === 'rejected')
                            <div class="mt-2 small">
                              <div class="text-muted">Maklumbalas admin:</div>
                              <div>{{ $r->admin_feedback ?? '—' }}</div>
                            </div>
                          @endif

                          @if($r->reviewed_at)
                            <div class="text-muted small mt-2">
                              Reviewed: {{ $r->reviewed_at ? $r->reviewed_at->format('d/m/Y H:i') : '-' }}
                            </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

            @php
              $latest = $latestProReq ?? null;
              $latestSt = strtolower((string)($latest->status ?? ''));
            @endphp

            @if($latestSt === 'pending')
              <div class="alert alert-info">
                <div class="fw-bold">Dalam Proses Semakan Oleh Pihak Admin</div>
                <div class="small text-muted">Sila tunggu keputusan admin untuk naik taraf Affiliate Pro.</div>
              </div>
            @elseif($latestSt === 'approved')
              <div class="alert alert-success">
                <div class="fw-bold">Permohonan diluluskan.</div>
                <div class="small text-muted">Anda kini Affiliate Pro.</div>
              </div>
            @else
              {{-- Show form (first time / or after rejected) --}}
              <form method="POST" action="{{ route('affiliate.pro.request') }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label fw-semibold">Platform promosi (wajib)</label>
                  <input class="form-control" name="promotion_platform" value="{{ old('promotion_platform') }}" maxlength="120" required
                         placeholder="Contoh: TikTok, Facebook, Instagram, WhatsApp, Telegram, YouTube, Blog">
                  @error('promotion_platform')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
                <div class="mb-2">
                  <label class="form-label fw-semibold">Sebab permohonan (wajib)</label>
                  <textarea class="form-control" name="reason" rows="4" required minlength="10" maxlength="2000"
                            placeholder="Contoh: Saya mempunyai audience yang sesuai dan ingin bantu lebih ramai..." >{{ old('reason') }}</textarea>
                  @error('reason')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit Permohonan</button>
              </form>
            @endif

          </div>
        </div>
      </div>
    </div>
    @endif
</section>

<script>
    // ni js untuk about me 

    var toggleAbout = document.getElementById('toggleAbout');
    if (toggleAbout) {
        toggleAbout.addEventListener('click', function() {
            const text = document.getElementById('aboutText');
            const toggle = this;
            if (!text) return;
            text.classList.toggle('expanded');
            toggle.textContent = text.classList.contains('expanded') ? 'Tutup ←' : 'Lihat lebih →';
        });
    }

    // ni js untuk copy link affiliate
    function copyLink(text) {
        navigator.clipboard.writeText(text);
        showToast('Link berjaya disalin!');
    }
    
    function showToast(msg) {
        const toast = document.createElement('div');
        toast.innerText = msg;
        toast.className = 'toast-msg';
        document.body.appendChild(toast);
    
        setTimeout(() => toast.remove(), 2500);
    }

    </script>
    

@if(config('analytics.google_measurement_id'))
@push('scripts')
<script>
(function () {
    if (typeof gtag !== 'function') return;
    gtag('event', 'view_profile', { profile_type: 'own' });
})();
</script>
@endpush
@endif

@include('partials.footer-nav', ['active' => 'profile'])
@endsection