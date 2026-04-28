<link rel="stylesheet" type="text/css" href="{{ asset('css/onboarding.css') }}">

@extends('layouts.app')

@section('content')
<div class="onboarding-wrapper">
    <div class="onboarding-card animate-in">
<h3 class="mb-3">Laluan Monogami</h3>

<p>
    Berdasarkan pilihan yang anda buat, anda berada di <strong>laluan monogami</strong>.
</p>

<ul>
    <li>Anda akan dipadankan dengan calon yang diisytiharkan dirinya mahu suami monogami sahaja dan juga calon yang isytiharkan tidak kisah monogami ATAU poligami.</li>
    <li>Calon wanita yang fokus kepada laluan poligami <strong>tidak akan dipaparkan</strong> dalam senarai anda.</li>
    <li>Jika suatu hari nanti status anda berubah dan ingin memohon laluan poligami,
        ia memerlukan <strong>semakan khas dan kelulusan pihak JodohMurni</strong>.</li>
</ul>
</br>
<p class="mb-3">
    Dengan meneruskan, anda mengesahkan bahawa anda sedang mencari jodoh untuk
    <strong>perkahwinan monogami</strong>.
</p>

<div class="d-flex gap-2">
    {{-- Butang ini SEKARANG buka modal, bukan terus redirect --}}
    <button type="button" class="gender-btn btn-primary" id="btnOpenSemakModal">
        Saya setuju &amp; teruskan (Upload Gambar)
    </button>

    <a href="{{ route('onboarding.male.status') }}" class="btn btn-outline-secondary">
        Tukar semula pilihan status
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
    }
</style>

{{-- ================ VANILLA JS ================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const openBtn = document.getElementById('btnOpenSemakModal');
        const modal = document.getElementById('semakModalOverlay');
        const closeBtn = document.getElementById('btnCloseSemakModal');

        if (!modal || !openBtn) return;

        // Bila klik "Saya setuju & teruskan", buka modal
        openBtn.addEventListener('click', function (e) {
            e.preventDefault();
            modal.classList.add('active');
        });

        // Tutup modal bila klik "Tutup"
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                modal.classList.remove('active');
            });
        }

        // Tutup modal bila klik di luar kotak
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });
</script>
@endsection