<link rel="stylesheet" href="{{ asset('css/onboarding.css') }}">

@extends('layouts.app')

@section('content')
<div class="onboarding-wrapper">
    <div class="onboarding-card animate-in">
<h3 class="mb-3" data-translate="onb_fmono_title">Laluan Monogami (Wanita)</h3>

<p>
    <strong data-translate="onb_fmono_strong">Anda berada di laluan perkahwinan monogami</strong>.
</p>

<ul>
    <li data-translate="onb_fmono_point_1">Padanan anda terhad kepada lelaki yang mahukan monogami atau bersikap terbuka.</li>
    <li data-translate="onb_fmono_point_2">Perubahan ke laluan poligami memerlukan kelulusan JodohMurni</li>
</ul>

<p class="mb-3">
    <span data-translate="onb_fmono_confirm_prefix">Dengan meneruskan, anda mengesahkan pencarian jodoh untuk</span>
    <strong data-translate="onb_fmono_confirm_strong">perkahwinan monogami</strong>.
</p>

<div class="d-flex gap-2">
    <button type="button" class="gender-btn" id="btnOpenSemakModal">
        <span data-translate="onb_mono_agree_open_modal">Saya setuju &amp; teruskan (Upload Gambar)</span>
    </button>

    <a href="{{ route('onboarding.female.preference') }}" class="btn btn-outline-secondary">
        <span data-translate="onb_back_change_status">Tukar semula pilihan</span>
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

        openBtn.addEventListener('click', function (e) {
            e.preventDefault();
            modal.classList.add('active');
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                modal.classList.remove('active');
            });
        }

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });
</script>
</div></div>
@endsection