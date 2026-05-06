<link rel="stylesheet" href="{{ asset('css/onboarding.css') }}">

@extends('layouts.app')

@section('content')

@php
    /**
     * Tetapan status jenis lelaki mengikut Tahap wanita:
     *
     * Tahap 1:
     *  - Pilihan 1 : Default
     *  - Pilihan 2 : Upgrade (tak perlu bayar)
     *  - Pilihan 3 : Disabled
     *
     * Tahap 2:
     *  - Pilihan 1 : Upgrade (tak perlu bayar)
     *  - Pilihan 2 : Default
     *  - Pilihan 3 : Disabled
     *
     * Tahap 3:
     *  - Pilihan 1 : Disabled
     *  - Pilihan 2 : Disabled
     *  - Pilihan 3 : Default
     */
    $matrix = [
        1 => [1 => 'Default',                  2 => 'Upgrade (tak perlu bayar)', 3 => 'Disabled'],
        2 => [1 => 'Upgrade (tak perlu bayar)',2 => 'Default',                    3 => 'Disabled'],
        3 => [1 => 'Disabled',                 2 => 'Disabled',                   3 => 'Default'],
    ];

    $status = $matrix[$level] ?? $matrix[1];
@endphp

<div class="onboarding-wrapper">
    <div class="onboarding-card animate-in">

{{-- <h5 class="mt-3">Potensi Calon Dalam Sistem JodohMurni</h5> --}}

{{-- <ul class="list-group mb-3">
    @for($jenis = 1; $jenis <= 3; $jenis++)
        @php
            $label = 'Pilihan Tahap ' . $jenis;
            $st    = $status[$jenis] ?? '';
            $count = $stats[$jenis] ?? 0;
        @endphp
        <li class="list-group-item d-flex justify-content-between align-items-center
            @if($st === 'Disabled') opacity-50 @endif">

            <div>
                <strong>{{ $label }}</strong>
                @if($st)
                    <span class="badge bg-light text-dark border ms-1">{{ $st }}</span>
                @endif

                <div class="small text-muted mt-1">
                    @if($jenis === 1)
                        Isteri sudah dimaklumkan mengenai hasrat poligami.
                    @elseif($jenis === 2)
                        Isteri akan dimaklumkan apabila ta'aruf berjaya.
                    @else
                        Isteri tidak akan dimaklumkan tentang poligami.
                    @endif
                </div>
            </div>

            <div class="text-end">
                <div>
                    <span class="badge bg-primary rounded-pill">
                        {{ $count }}
                    </span>
                </div>

                @if(strpos($st, 'Upgrade') === 0)
                    <div class="mt-1">
                        <button type="button" class="btn btn-sm btn-outline-success">
                            Upgrade (Demo, percuma)
                        </button>
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                        *Butang demo sahaja – belum sambung payment / logik sebenar.
                    </small>
                @endif
            </div>
        </li>
    @endfor
</ul> --}}

<hr>

<div class="mt-4 d-flex gap-2">
    <button type="button" class="gender-btn" id="btnOpenSemakModal">
        <span data-translate="onb_poly_f_continue_photos">Saya faham &amp; mahu teruskan (Upload Gambar)</span>
    </button>

    <a href="{{ route('onboarding.female.polygamy_level') }}" class="btn btn-outline-secondary">
        <span data-translate="onb_fpoly_back_change_model">Kembali ubah Model</span>
    </a>
</div>

{{-- =============== MODAL: SEMAK & IMBANG SALAH LAKU =============== --}}
<div id="semakModalOverlay" class="jm-modal-overlay">
    <div class="jm-modal">
        <h5 class="mb-3" data-translate="onb_semak_title">Semak &amp; Imbang Salah Laku</h5>

        <ul>
            <li>
                <span data-translate="onb_semak_point_1_prefix">Calon diberi ruang melaporkan sebarang salah laku dalam tempoh</span>
                <strong data-translate="onb_semak_point_1_strong">5 hari</strong>
                <span data-translate="onb_semak_point_1_tail">dari perbualan pertama direkodkan.</span>
            </li>
            <li>
                <span data-translate="onb_semak_point_2">Laporan dinilai secara manual oleh pihak JodohMurni.</span>
            </li>
            <li>
                <span data-translate="onb_semak_actions">Tindakan yang boleh diambil:</span>
                <ul>
                    <li>
                        <span data-translate="onb_semak_action_redcard_prefix">Amaran</span>
                        <strong data-translate="onb_semak_action_redcard_strong">Kad Merah</strong>
                    </li>
                    <li>
                        <strong data-translate="onb_semak_action_suspend_strong">Penggantungan akaun</strong>
                        <span data-translate="onb_semak_action_suspend_tail">15–30 hari</span>
                    </li>
                    <li><strong data-translate="onb_semak_action_terminate">Penamatan akaun</strong></li>
                </ul>
            </li>
        </ul>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" id="btnCloseSemakModal">
                <span data-translate="onb_close">Tutup</span>
            </button>
            <a href="{{ route('onboarding.photos') }}" class="gender-btn">
                <span data-translate="onb_semak_understand_continue">Saya faham &amp; teruskan upload gambar</span>
            </a>
        </div>
    </div>
</div>

{{-- =============== AUTO-POPUP: DYNAMIK MENGIKUT $level =============== --}}
<div id="welcomePopup" class="jm-modal-overlay">
    <div class="jm-modal">
        <h5 class="mb-3" data-translate="onb_fpoly_summary_title">Ringkasan Laluan Poligami (Wanita)</h5>

        @if($level === 1)
            <p>
                <span data-translate="onb_fpoly_summary_l1_prefix">Anda memilih</span> <strong data-translate="onb_fpoly_model_1">Model 1</strong>
                <span data-translate="onb_fpoly_summary_l1_tail">– anda hanya selesa dengan calon suami yang sudah memaklumkan isterinya tentang hasrat poligami.</span>
            </p>
        @elseif($level === 2)
            <p>
                <span data-translate="onb_fpoly_summary_l2_prefix">Anda memilih</span> <strong data-translate="onb_fpoly_model_2">Model 2</strong>
                <span data-translate="onb_fpoly_summary_l2_tail">– anda tidak kisah sama ada calon suami sudah memaklumkan isterinya atau belum, asalkan proses ta'aruf berjalan dengan baik.</span>
            </p>
        @else
            <p>
                <span data-translate="onb_fpoly_summary_l3_prefix">Anda memilih</span> <strong data-translate="onb_fpoly_model_3">Model 3</strong>
                <span data-translate="onb_fpoly_summary_l3_tail">– anda terbuka kepada calon suami yang tidak memaklumkan isterinya berkenaan poligami.</span>
            </p>
        @endif

        {{-- <h6 class="mt-3">Pilihan Calon Suami (Situasi Lelaki)</h6>
        <ul class="small mb-0">
            <li><strong>Pilihan Tahap 1</strong> – Lelaki yang sudah memaklumkan isterinya tentang hasrat poligami.</li>
            <li><strong>Pilihan Tahap 2</strong> – Lelaki yang akan memaklumkan isterinya apabila calon setuju.</li>
            <li><strong>Pilihan Tahap 3</strong> – Lelaki yang tidak bercadang memaklumkan isterinya.</li>
        </ul> --}}

        <div class="mt-4 d-flex justify-content-end">
            <button type="button" class="gender-btn" id="closeWelcomePopup" data-translate="onb_ok_understand">OK, Saya Faham</button>
        </div>
    </div>
</div>

{{-- ================ SIMPLE MODAL CSS ================= --}}
<style>
    .jm-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }
    .jm-modal-overlay.active {
        display: flex;
    }
    .jm-modal {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px 24px;
        max-width: 520px;
        width: 90%;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        font-size: 0.95rem;
    }
</style>

{{-- ================ VANILLA JS ================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // === AUTO-POPUP (WELCOME) ===
        const welcomePopup = document.getElementById('welcomePopup');
        const closeWelcomeBtn = document.getElementById('closeWelcomePopup');

        if (welcomePopup) {
            welcomePopup.classList.add('active');

            if (closeWelcomeBtn) {
                closeWelcomeBtn.addEventListener('click', function () {
                    welcomePopup.classList.remove('active');
                });
            }

            welcomePopup.addEventListener('click', function (e) {
                if (e.target === welcomePopup) {
                    welcomePopup.classList.remove('active');
                }
            });
        }

        // === SEMAK MODAL (bila klik butang utama) ===
        const openSemakBtn = document.getElementById('btnOpenSemakModal');
        const semakModal = document.getElementById('semakModalOverlay');
        const closeSemakBtn = document.getElementById('btnCloseSemakModal');

        if (openSemakBtn && semakModal) {
            openSemakBtn.addEventListener('click', function (e) {
                e.preventDefault();
                semakModal.classList.add('active');
            });

            if (closeSemakBtn) {
                closeSemakBtn.addEventListener('click', function () {
                    semakModal.classList.remove('active');
                });
            }

            semakModal.addEventListener('click', function (e) {
                if (e.target === semakModal) {
                    semakModal.classList.remove('active');
                }
            });
        }
    });
</script>
</div>
</div>
@endsection