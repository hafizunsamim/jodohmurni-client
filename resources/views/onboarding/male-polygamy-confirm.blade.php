<link rel="stylesheet" type="text/css" href="{{ asset('css/onboarding.css') }}">
@extends('layouts.app')

@section('content')

<div class="onboarding-wrapper">
    <div class="onboarding-card animate-in">
<p class="mb-3">
    Adakah anda sedar bahawa melalui pilihan status tadi,
    anda sedang memilih untuk <strong>bertemu jodoh poligami</strong>?
</p>

<p class="mb-2">
    Sila buat pilihan di bawah:
</p>

<form method="POST" action="{{ url('/onboarding/male/polygamy-confirm') }}" id="polygamyConfirmForm">
    @csrf

    {{-- hidden input untuk dihantar bila user sahkan dalam modal --}}
    <input type="hidden" name="confirm" id="confirmInput" value="">
    <div class="row mb-3">
    <button type="button" class="btn btn-danger me-2" id="btnPolygamyYes">
        Ya, saya sedar &amp; mahu teruskan
    </button>
</div>
    <div class="row">
    {{-- yang ini terus submit sebagai "no" tanpa modal --}}
    <button type="submit" name="confirm" value="no" class="btn btn-outline-secondary">
        Tidak &amp; kembali ke pilihan status
    </button>
    </div>
</form>

{{-- ================= MODAL PERINGATAN RINGKAS ================= --}}
<div id="polygamyModal" class="jm-modal-overlay">
    <div class="jm-modal">
        <h5 class="mb-3">Peringatan Ringkas</h5>

        <p>
            Laluan poligami memerlukan <strong>ketelusan tinggi</strong> terhadap isteri sedia ada dan calon.
        </p>
        {{-- <p>
            Selepas anda kekalkan laluan ini, keseluruhan modul &amp; calon
            <strong>monogami tidak lagi berfungsi</strong> untuk akaun ini.
        </p> --}}

        <div class="mt-3 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" id="btnPolygamyCancel">
                Kembali
            </button>
            <button type="button" class="btn btn-danger" id="btnPolygamyProceed">
                Saya faham &amp; mahu teruskan
            </button>
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
        display: none;              /* default: hidden */
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

{{-- ================ MODAL JS ================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form        = document.getElementById('polygamyConfirmForm');
        const overlay     = document.getElementById('polygamyModal');
        const btnYes      = document.getElementById('btnPolygamyYes');
        const btnProceed  = document.getElementById('btnPolygamyProceed');
        const btnCancel   = document.getElementById('btnPolygamyCancel');
        const confirmInput = document.getElementById('confirmInput');

        if (!form || !overlay || !btnYes || !btnProceed || !btnCancel || !confirmInput) {
            return;
        }

        // Bila klik "Ya, saya sedar & mahu teruskan" -> buka modal
        btnYes.addEventListener('click', function () {
            overlay.classList.add('active');
        });

        // Dalam modal: "Saya faham & mahu teruskan" -> set confirm=yes & submit form
        btnProceed.addEventListener('click', function () {
            confirmInput.value = 'yes';
            form.submit();
        });

        // Dalam modal: "Kembali" -> tutup modal
        btnCancel.addEventListener('click', function () {
            overlay.classList.remove('active');
        });

        // Klik luar kotak modal pun boleh tutup
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                overlay.classList.remove('active');
            }
        });
    });
</script>
@endsection
