<link rel="stylesheet" href="{{ asset('css/onboarding.css') }}">

@extends('layouts.app')

@section('content')

@php
    /**
     * Tetapan status Tahap mengikut situasi lelaki (poligami_situation):
     *
     * PILIHAN 1 (situation = 1)
     *   - Tahap 1  : Akan dipaparkan dahulu (Default 1)
     *   - Tahap 2  : Akan dipaparkan berikutnya (Default 2)
     *   - Tahap 3  : Tidak akan dipaparkan (Disabled)
     *
     * PILIHAN 2 (situation = 2)
     *   - Tahap 1  : Tidak akan dipaparkan (Disabled)
     *   - Tahap 2  : Akan dipaparkan dahulu (Default 1)
     *   - Tahap 3  : Sila Upgrade (Default 2)
     *
     * PILIHAN 3 (situation = 3)
     *   - Tahap 1  : Tidak akan dipaparkan (Disabled)
     *   - Tahap 2  : Tidak akan dipaparkan (Disabled)
     *   - Tahap 3  : Akan dipaparkan dahulu (Default 1)
     */

    // Struktur asas Tahap
    $tahap = [
        1 => ['label' => 'Tahap 1', 'status_badge' => '', 'desc' => '', 'upgrade' => false],
        2 => ['label' => 'Tahap 2', 'status_badge' => '', 'desc' => '', 'upgrade' => false],
        3 => ['label' => 'Tahap 3', 'status_badge' => '', 'desc' => '', 'upgrade' => false],
    ];

    if ($situation === 1) {
        // PILIHAN 1
        $tahap[1]['status_badge'] = 'Default 1';
        $tahap[1]['desc']         = 'Akan dipaparkan dahulu.';

        $tahap[2]['status_badge'] = 'Default 2';
        $tahap[2]['desc']         = 'Akan dipaparkan berikutnya.';

        $tahap[3]['status_badge'] = 'Disabled';
        $tahap[3]['desc']         = 'Tidak akan dipaparkan.';

    } elseif ($situation === 2) {
        // PILIHAN 2
        $tahap[1]['status_badge'] = 'Disabled';
        $tahap[1]['desc']         = 'Tidak akan dipaparkan.';

        $tahap[2]['status_badge'] = 'Default 1';
        $tahap[2]['desc']         = 'Akan dipaparkan dahulu.';

        $tahap[3]['status_badge'] = 'Default 2';
        $tahap[3]['desc']         = 'Sila upgrade.';
        $tahap[3]['upgrade']      = true;

    } else {
        // PILIHAN 3
        $tahap[1]['status_badge'] = 'Disabled';
        $tahap[1]['desc']         = 'Tidak akan dipaparkan.';

        $tahap[2]['status_badge'] = 'Disabled';
        $tahap[2]['desc']         = 'Tidak akan dipaparkan.';

        $tahap[3]['status_badge'] = 'Default 1';
        $tahap[3]['desc']         = 'Akan dipaparkan dahulu.';
    }

    // ================== TEKS POPUP MENGIKUT SITUATION ==================

    $welcome = [
        'title'    => 'Ringkasan Laluan Poligami',
        'headline' => '',
        'body'     => '',
    ];

    if ($situation === 1) {
        // PILIHAN 1 – Isteri sudah maklum
        $welcome['headline'] = 'Tahniah kerana memilih jalan sebagai “Lelaki Qawwam”.';
        $welcome['body'] = 'Anda mengakui bahawa isteri sudah sedia maklum hasrat anda ingin berpoligami. Ini selari dengan roh maqasid poligami yang menekankan keadilan dan ketelusan.';
    } elseif ($situation === 2) {
        // PILIHAN 2 – Isteri akan dimaklum bila calon setuju
        $welcome['headline'] = 'Anda memilih untuk berpoligami dengan komitmen akan memaklumkan isteri apabila calon bersetuju.';
        $welcome['body'] = 'JodohMurni akan mengutamakan padanan dengan calon yang tidak kisah sama ada isteri sudah dimaklum atau belum. Namun, sistem tetap menegaskan kepentingan ketelusan dan keadilan dalam rumah tangga.';
    } else {
        // PILIHAN 3 – Isteri tidak akan dimaklum
        $welcome['headline'] = 'Anda memilih untuk berpoligami tanpa merancang memaklumkan isteri.';
        $welcome['body'] = 'JodohMurni menekankan amanah, maruah dan keselamatan calon wanita. Pemilihan ini mempunyai implikasi besar terhadap kepercayaan dan masa depan semua pihak. Sila pastikan anda benar-benar faham dan bersedia menanggung akibat pilihan ini.';
    }
@endphp

<div class="onboarding-wrapper">
    <div class="onboarding-card animate-in">

{{-- <h5 class="mt-3">Potensi Calon Dalam Sistem JodohMurni</h5> --}}

{{-- <ul class="list-group mb-3">
    @foreach($tahap as $level => $row)
        <li class="list-group-item d-flex justify-content-between align-items-center
            @if($row['status_badge'] === 'Disabled') opacity-50 @endif">

            <div>
                <strong>{{ $row['label'] }}</strong>
                @if($row['status_badge'])
                    <span class="badge bg-light text-dark border ms-1">
                        {{ $row['status_badge'] }}
                    </span>
                @endif

                @if(!empty($row['desc']))
                    <div class="small text-muted mt-1">
                        {{ $row['desc'] }}
                    </div>
                @endif
            </div>

            <div class="text-end">
                <div>
                    <span class="badge bg-primary rounded-pill">
                        {{ $stats[$level] ?? 0 }}
                    </span>
                </div>

                @if(!empty($row['upgrade']) && $row['upgrade'] === true)
                    <form action="#" method="POST" class="mt-1">
                        @csrf
                        <button type="button" class="btn btn-sm btn-outline-success">
                            Upgrade &amp; Bayar (Demo)
                        </button>
                    </form>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                        *Butang demo sahaja – belum sambung payment gateway.
                    </small>
                @endif
            </div>
        </li>
    @endforeach
</ul> --}}

<hr>

<div class="mt-4 d-flex gap-2">
    <button type="button" class="gender-btn" id="btnOpenSemakModal">
        Saya faham &amp; mahu teruskan (Upload Gambar)
    </button>

    <a href="{{ route('onboarding.male.polygamy_situation') }}" class="btn btn-outline-secondary">
        Kembali ubah situasi isteri
    </a>
</div>

{{-- =============== MODAL: SEMAK & IMBANG SALAH LAKU =============== --}}
<div id="semakModalOverlay" class="jm-modal-overlay">
    <div class="jm-modal">
        <h5 class="mb-3">Semak &amp; Imbang Salah Laku</h5>

        <ul>
            <li>
                Calon diberi ruang melaporkan sebarang salah laku dalam tempoh
                <strong>5 hari</strong> dari perbualan pertama direkodkan.
            </li>
            <li>
                Laporan dinilai secara manual oleh pihak JodohMurni.
            </li>
            <li>
                Tindakan yang boleh diambil:
                <ul>
                    <li>Amaran <strong>Kad Merah</strong></li>
                    <li><strong>Penggantungan akaun</strong> 15–30 hari</li>
                    <li><strong>Penamatan akaun</strong></li>
                </ul>
            </li>
        </ul>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" id="btnCloseSemakModal">
                Tutup
            </button>
            <a href="{{ route('onboarding.photos') }}" class="gender-btn">
                Saya faham &amp; teruskan upload gambar
            </a>
        </div>
    </div>
</div>

{{-- =============== AUTO-POPUP (WELCOME) =============== --}}
<div id="welcomePopup" class="jm-modal-overlay">
    <div class="jm-modal">
        <h5 class="mb-3">{{ $welcome['title'] }}</h5>

        <p><strong>{{ $welcome['headline'] }}</strong></p>

        <p>{{ $welcome['body'] }}</p>

        {{-- <h6 class="mt-3">JodohMurni Mengkelaskan Calon Wanita Poligami kepada 3 Tahap</h6>
        <ul class="small mb-0">
            <li><strong>Pilihan Tahap 1</strong> – Wanita yang mencari calon suami dan
                <strong>calon suami sudah memaklumkan isterinya</strong> tentang hasrat poligami.</li>
            <li><strong>Pilihan Tahap 2</strong> – Wanita yang <strong>tidak kisah</strong> sama ada calon suami
                sudah memaklumkan atau belum kepada isterinya.</li>
            <li><strong>Pilihan Tahap 3</strong> – Wanita yang <strong>tidak mahu calon suami maklumkan langsung</strong>
                hasrat poligami kepada isterinya.</li>
        </ul> --}}

        <div class="mt-4 d-flex justify-content-end">
            <button type="button" class="gender-btn" id="closeWelcomePopup">OK, Saya Faham</button>
        </div>
    </div>
</div>

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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // === AUTO-POPUP (WELCOME) ===
        const welcomeModal   = document.getElementById('welcomePopup');
        const closeWelcomeBtn = document.getElementById('closeWelcomePopup');

        if (welcomeModal) {
            welcomeModal.classList.add('active'); // muncul sebaik page load

            if (closeWelcomeBtn) {
                closeWelcomeBtn.addEventListener('click', function () {
                    welcomeModal.classList.remove('active');
                });
            }

            // Tutup jika klik luar
            welcomeModal.addEventListener('click', function (e) {
                if (e.target === welcomeModal) {
                    welcomeModal.classList.remove('active');
                }
            });
        }

        // === SEMAK & IMBANG MODAL ===
        const openBtn      = document.getElementById('btnOpenSemakModal');
        const semakModal   = document.getElementById('semakModalOverlay');
        const closeSemakBtn = document.getElementById('btnCloseSemakModal');

        if (openBtn && semakModal) {
            openBtn.addEventListener('click', function (e) {
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
