<link rel="stylesheet" href="{{ asset('css/onboarding.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@extends('layouts.app')

@section('content')
<div class="onboarding-wrapper">
    <div class="onboarding-card animate-in">
        <h3 class="fw-bold mb-2">Pilih situasi yang paling menggambarkan keadaan anda</h3>
        <p class="text-muted mb-3">Sila pilih satu pilihan sahaja</p>

        <form method="POST" action="{{ url('/onboarding/male/polygamy-situation') }}" id="polygamySituationForm">
            @csrf

            <input type="hidden" name="poligami_situation" id="poligami_situation"
                   value="{{ old('poligami_situation', '') }}"
                   data-old-value="{{ old('poligami_situation', '') }}">

            <div class="jm-poly-stack" role="listbox" aria-label="Situasi poligami">
                <button type="button" class="jm-poly-btn" data-value="1" aria-selected="false">
                    <span class="jm-poly-btn__icon jm-poly-btn__icon--ok" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                    <span class="jm-poly-btn__text">
                        <span class="jm-poly-btn__title">Isteri sudah dimaklumkan</span>
                        <span class="jm-poly-btn__sub">Isteri saya sudah mengetahui hasrat saya untuk berpoligami.</span>
                        <span class="jm-poly-btn__note jm-poly-btn__note--good">
                            <span class="jm-poly-btn__note-icon" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
                            <span class="jm-poly-btn__note-strong">Disyorkan</span>
                            <span class="jm-poly-btn__note-text">untuk hubungan lebih telus</span>
                        </span>
                    </span>
                    <span class="jm-poly-btn__trail" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
                </button>

                <button type="button" class="jm-poly-btn" data-value="2" aria-selected="false">
                    <span class="jm-poly-btn__icon jm-poly-btn__icon--wait" aria-hidden="true"><i class="bi bi-clock"></i></span>
                    <span class="jm-poly-btn__text">
                        <span class="jm-poly-btn__title">Isteri akan dimaklumkan kemudian</span>
                        <span class="jm-poly-btn__sub">Isteri saya akan dimaklumkan apabila calon bersetuju untuk meneruskan proses poligami.</span>
                    </span>
                    <span class="jm-poly-btn__trail" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
                </button>

                <button type="button" class="jm-poly-btn" data-value="3" aria-selected="false">
                    <span class="jm-poly-btn__icon jm-poly-btn__icon--sensitive" aria-hidden="true"><i class="bi bi-eye-slash"></i></span>
                    <span class="jm-poly-btn__text">
                        <span class="jm-poly-btn__title">Isteri tidak akan dimaklumkan</span>
                        <span class="jm-poly-btn__sub">Isteri saya tidak akan dimaklumkan walaupun calon bersetuju untuk poligami.</span>
                        <span class="jm-poly-btn__note jm-poly-btn__note--warn">
                            <span class="jm-poly-btn__note-icon" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
                            <span class="jm-poly-btn__note-strong">Sensitif</span>
                            <span class="jm-poly-btn__note-text">boleh jejaskan kepercayaan isteri</span>
                        </span>
                    </span>
                    <span class="jm-poly-btn__trail" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
                </button>
            </div>

            @error('poligami_situation')
                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
            @enderror
        </form>
    </div>
</div>
@endsection

<style>
.jm-poly-stack { display: flex; flex-direction: column; gap: 14px; }
.jm-poly-btn {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    width: 100%;
    text-align: left;
    padding: 14px 48px 14px 14px;
    border-radius: 16px;
    border: 1px solid rgba(20, 94, 62, 0.18);
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    cursor: pointer;
    position: relative;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background 0.15s ease;
}
.jm-poly-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(46,125,50,0.12);
    border-color: rgba(20, 94, 62, 0.28);
}
.jm-poly-btn__icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eef7f2;
    color: #145e3e;
    font-size: 1.3rem;
    border: 1px solid #e2e8f0;
    margin-top: 2px;
}
.jm-poly-btn__icon--ok { background: #e6f7ef; color: #145e3e; }
.jm-poly-btn__icon--wait { background: #eef3f7; color: #4b5563; }
.jm-poly-btn__icon--sensitive { background: #f3f4f6; color: #374151; }

.jm-poly-btn__text { flex: 1; min-width: 0; }
.jm-poly-btn__title {
    display: block;
    font-weight: 800;
    font-size: 1.02rem;
    color: #111827;
    margin-bottom: 6px;
}
.jm-poly-btn__sub {
    display: block;
    font-size: 0.9rem;
    line-height: 1.35;
    color: #6b7280;
}
.jm-poly-btn__note {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 0.85rem;
    line-height: 1;
    white-space: nowrap;
}
.jm-poly-btn__note-icon { display: inline-flex; align-items: center; justify-content: center; }
.jm-poly-btn__note-strong { font-weight: 800; }
.jm-poly-btn__note-text { opacity: 0.9; }
.jm-poly-btn__note--good { background: #e8f6ee; color: #145e3e; }
.jm-poly-btn__note--warn { background: #f7efe3; color: #8a5a12; }

.jm-poly-btn__trail {
    position: absolute;
    top: 12px;
    right: 12px;
    color: #145e3e;
    font-size: 1.15rem;
    opacity: 0;
    transform: translateY(2px);
    pointer-events: none;
    transition: opacity 0.15s ease, transform 0.15s ease, color 0.15s ease;
}
.jm-poly-btn.selected {
    background: linear-gradient(135deg, #e7f6ef 0%, #cfeedd 70%, #c3e8d3 100%);
    border: 2px solid #2e7d32;
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.18), 0 10px 28px rgba(27, 94, 32, 0.18);
}
.jm-poly-btn.selected .jm-poly-btn__sub { color: #145e3e; }
.jm-poly-btn.selected .jm-poly-btn__icon {
    background: #145e3e;
    border-color: rgba(20, 94, 62, 0.35);
    color: #fff;
    box-shadow: 0 0 0 6px rgba(20, 94, 62, 0.12);
}
.jm-poly-btn.selected .jm-poly-btn__trail {
    opacity: 1;
    transform: translateY(0);
    color: #145e3e;
}

@media (max-width: 575.98px) {
    .jm-poly-stack { gap: 12px; }
    .jm-poly-btn {
        padding: 12px 42px 12px 12px;
        gap: 10px;
        border-radius: 14px;
    }
    .jm-poly-btn__icon { width: 40px; height: 40px; font-size: 1.05rem; border-radius: 11px; }
    .jm-poly-btn__title { font-size: 0.95rem; margin-bottom: 3px; line-height: 1.28; }
    .jm-poly-btn__sub { font-size: 0.82rem; line-height: 1.32; }
    .jm-poly-btn__trail { top: 10px; right: 10px; font-size: 1.05rem; }

    /* pill label: kemas, seimbang, tak 'stretch' penuh */
    .jm-poly-btn__note {
        display: inline-flex;
        width: fit-content;
        max-width: 100%;
        justify-content: flex-start;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 8px;
        padding: 6px 10px;
        font-size: 0.76rem;
        line-height: 1.15;
        white-space: normal;
        border-radius: 999px;
        box-sizing: border-box;
        overflow: hidden;
    }
    .jm-poly-btn__note-strong { white-space: nowrap; }
    .jm-poly-btn__note-text {
        display: inline;
        min-width: 0;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: normal;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("polygamySituationForm");
    const hidden = document.getElementById("poligami_situation");
    const oldVal = hidden?.getAttribute("data-old-value") || "";
    let submitting = false;

    function clearSelection() {
        document.querySelectorAll(".jm-poly-btn").forEach(function (btn) {
            btn.classList.remove("selected");
            btn.setAttribute("aria-selected", "false");
        });
        hidden.value = "";
    }

    function applyValue(val) {
        clearSelection();
        if (!val) return;
        const btn = document.querySelector('.jm-poly-btn[data-value="' + val + '"]');
        if (btn) {
            btn.classList.add("selected");
            btn.setAttribute("aria-selected", "true");
        }
        hidden.value = val;
    }

    document.querySelectorAll(".jm-poly-btn").forEach(function (btn) {
        btn.addEventListener("click", function () {
            if (submitting) return;
            const val = this.getAttribute("data-value");
            applyValue(val);
            if (!hidden.value) return;
            submitting = true;
            document.querySelectorAll(".jm-poly-btn").forEach(function (b) { b.disabled = true; });
            form.submit();
        });
    });

    if (oldVal) applyValue(oldVal);
});
</script>
@endpush
