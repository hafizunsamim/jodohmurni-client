<link rel="stylesheet" type="text/css" href="{{ asset('css/onboarding.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@extends('layouts.app')

@section('content')
<div class="onboarding-wrapper">
    <div class="onboarding-card animate-in">

        <!-- Progress (next step, example 4/6 = 65%) -->
        <div class="progress mb-4" style="height:6px;">
            <div class="progress-bar bg-success" style="width:65%"></div>
        </div>

        <h4 class="fw-bold mb-2 text-center">Pilih Jenis Perkahwinan Yang Anda Cari</h4>

        <form action="{{ url('/onboarding/male/status') }}" method="POST" class="mt-2" id="maleStatusForm">
            @csrf

            {{-- Pilihan laluan: Monogami / Poligami (kad ikon + subtitle) --}}
            <div class="mb-4 jm-path-wrap">
                <div class="row g-2 g-md-3 jm-path-row align-items-stretch">
                    <div class="col-6 jm-path-col">
                        <input type="radio" name="path_type" id="path_monogami" value="monogami" class="jm-path-radio" autocomplete="off"
                            {{ in_array(old('marital_status'), ['single','divorced','widowed','ex_polygamous']) ? 'checked' : '' }}>
                        <label class="jm-path-card jm-path-card--monogami" for="path_monogami">
                            <span class="jm-path-card__indicator" aria-hidden="true"></span>
                            <span class="jm-path-card__top">
                                <span class="jm-path-card__icon">
                                    <img src="{{ asset('assets/images/icon-monogami.png') }}" alt="">
                                </span>
                                <span class="jm-path-card__title">Monogami</span>
                            </span>
                            <span class="jm-path-card__bottom">Mencari satu pasangan untuk perkahwinan</span>
                        </label>
                    </div>
                    <div class="col-6 jm-path-col">
                        <input type="radio" name="path_type" id="path_poligami" value="poligami" class="jm-path-radio" autocomplete="off"
                            {{ in_array(old('marital_status'), ['married_1','married_2','married_3']) ? 'checked' : '' }}>
                        <label class="jm-path-card jm-path-card--poligami" for="path_poligami">
                            <span class="jm-path-card__indicator" aria-hidden="true"></span>
                            <span class="jm-path-card__top">
                                <span class="jm-path-card__icon jm-path-card__icon--poligami">
                                    <img src="{{ asset('assets/images/icon-poligami.png') }}" alt="">
                                </span>
                                <span class="jm-path-card__title">Poligami</span>
                            </span>
                            <span class="jm-path-card__bottom">Mencari pasangan dalam perkahwinan poligami</span>
                        </label>
                    </div>
                </div>
                @error('path_type')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            @php
                $optionsMonogami = [
                    'single'        => 'Bujang',
                    'divorced'      => 'Duda (cerai hidup)',
                    'widowed'       => 'Duda (kematian isteri)',
                    'ex_polygamous' => 'Duda (pernah poligami)',
                ];
                $optionsPoligami = [
                    'married_1' => 'Sedang berkahwin dengan seorang isteri',
                    'married_2' => 'Sedang berkahwin dengan 2 orang isteri',
                    'married_3' => 'Sedang berkahwin dengan 3 orang isteri',
                ];
                $cardMonogami = [
                    'single'        => ['title' => 'BUJANG', 'subtitle' => 'Belum pernah berkahwin', 'icon' => 'bi-heart'],
                    'divorced'      => ['title' => 'DUDA (CERAI)', 'subtitle' => 'Pernah berkahwin & telah berpisah', 'icon' => 'bi-arrow-repeat'],
                    'widowed'       => ['title' => 'DUDA (KEMATIAN ISTERI)', 'subtitle' => 'Kehilangan pasangan', 'icon' => 'bi-heart-pulse'],
                    'ex_polygamous' => ['title' => 'DUDA (PERNAH POLIGAMI)', 'subtitle' => 'Berpengalaman dalam poligami', 'icon' => 'bi-people'],
                ];
                $cardPoligami = [
                    'married_1' => ['title' => 'SATU ISTERI', 'subtitle' => 'Sedang berkahwin dengan seorang isteri', 'icon' => 'bi-person-fill'],
                    'married_2' => ['title' => 'DUA ISTERI', 'subtitle' => 'Sedang berkahwin dengan 2 orang isteri', 'icon' => 'bi-people'],
                    'married_3' => ['title' => 'TIGA ISTERI', 'subtitle' => 'Sedang berkahwin dengan 3 orang isteri', 'icon' => 'bi-people-fill'],
                ];
                $oldMarital = old('marital_status', '');
            @endphp

            <div id="statusPerkahwinanSection" class="mb-3" style="display: none;">
                <p class="form-label fw-semibold mb-2">Status Perkahwinan</p>

                <input type="hidden" name="marital_status" id="marital_status" value="{{ $oldMarital }}"
                    data-old-value="{{ $oldMarital }}">

                <div id="maritalStackMonogami" class="jm-status-stack d-none" role="listbox" aria-label="Status monogami">
                    @foreach($cardMonogami as $val => $meta)
                        <button type="button" class="jm-status-btn" data-value="{{ $val }}" data-path="monogami"
                            aria-selected="{{ $oldMarital === $val ? 'true' : 'false' }}">
                            <span class="jm-status-btn__icon"><i class="bi {{ $meta['icon'] }}"></i></span>
                            <span class="jm-status-btn__text">
                                <span class="jm-status-btn__title">{{ $meta['title'] }}</span>
                                <span class="jm-status-btn__sub">{{ $meta['subtitle'] }}</span>
                            </span>
                            <span class="jm-status-btn__trail">
                                <span class="jm-status-btn__chev" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                                <span class="jm-status-btn__picked"><i class="bi bi-check-lg"></i> Diplilih</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                <div id="maritalStackPoligami" class="jm-status-stack d-none" role="listbox" aria-label="Status poligami">
                    @foreach($cardPoligami as $val => $meta)
                        <button type="button" class="jm-status-btn" data-value="{{ $val }}" data-path="poligami"
                            aria-selected="{{ $oldMarital === $val ? 'true' : 'false' }}">
                            <span class="jm-status-btn__icon"><i class="bi {{ $meta['icon'] }}"></i></span>
                            <span class="jm-status-btn__text">
                                <span class="jm-status-btn__title">{{ $meta['title'] }}</span>
                                <span class="jm-status-btn__sub">{{ $meta['subtitle'] }}</span>
                            </span>
                            <span class="jm-status-btn__trail">
                                <span class="jm-status-btn__chev" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                                <span class="jm-status-btn__picked"><i class="bi bi-check-lg"></i> Diplilih</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                @error('marital_status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </form>
    </div>
</div>
@endsection

<style>
/* Kad status perkahwinan (Monogami / Poligami) */
.jm-status-stack { display: flex; flex-direction: column; gap: 12px; }
.jm-status-btn {
    display: flex;
    align-items: center;
    gap: 14px;
    width: 100%;
    text-align: left;
    padding: 14px 16px;
    border-radius: 16px;
    border: 1px solid #d8e8dc;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background 0.15s ease;
}
.jm-status-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(46,125,50,0.12);
    border-color: #b2d3c2;
}
.jm-status-btn__icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f0f9f4;
    color: #1f6f2a;
    font-size: 1.35rem;
    border: 1px solid #e2e8f0;
}
.jm-status-btn__text { flex: 1; min-width: 0; }
.jm-status-btn__title {
    display: block;
    font-weight: 800;
    font-size: 0.95rem;
    letter-spacing: 0.02em;
    color: #1a2e1f;
    margin-bottom: 4px;
}
.jm-status-btn__sub {
    display: block;
    font-size: 0.82rem;
    line-height: 1.35;
    color: #5a6b5e;
}
.jm-status-btn__trail { flex-shrink: 0; display: flex; align-items: center; }
.jm-status-btn__chev { color: #2e7d32; font-size: 1.1rem; }
.jm-status-btn__picked {
    display: none;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #fff;
    white-space: nowrap;
}
.jm-status-btn.selected {
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 45%, #1b5e20 100%);
    border: 2px solid #c9a227;
    box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.35), 0 10px 28px rgba(27, 94, 32, 0.35);
}
.jm-status-btn.selected .jm-status-btn__title,
.jm-status-btn.selected .jm-status-btn__sub { color: #fff; }
.jm-status-btn.selected .jm-status-btn__icon {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.35);
    color: #fff;
    box-shadow: 0 0 12px rgba(255,255,255,0.25);
}
.jm-status-btn.selected .jm-status-btn__chev { display: none; }
.jm-status-btn.selected .jm-status-btn__picked { display: inline-flex; }

@media (max-width: 575.98px) {
    .jm-status-btn { padding: 12px 12px; gap: 10px; border-radius: 14px; }
    .jm-status-btn__icon { width: 42px; height: 42px; font-size: 1.15rem; }
    .jm-status-btn__title { font-size: 0.88rem; }
    .jm-status-btn__sub { font-size: 0.76rem; }
}

.jm-path-radio { position: absolute; opacity: 0; pointer-events: none; }
.jm-path-wrap { max-width: 520px; margin-left: auto; margin-right: auto; }
.jm-path-row.jm-path-row { align-items: stretch; }
.jm-path-col { display: flex; }
.jm-path-col .jm-path-radio { position: absolute; }
.jm-path-col .jm-path-card { width: 100%; flex: 1; min-width: 0; }
.jm-path-card {
    display: flex;
    flex-direction: column;
    width: 100%;
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    padding: 0;
    margin: 0;
    position: relative;
}
.jm-path-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.12); }

.jm-path-card__top {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.25rem 0.75rem 1rem;
    color: #fff;
    flex-shrink: 0;
    min-height: 140px;
    height: 140px;
}
.jm-path-card__bottom {
    display: block;
    padding: 0.75rem 0.5rem;
    background: #fff;
    color: #333;
    font-size: 0.85rem;
    text-align: center;
    line-height: 1.3;
    flex: 1;
    min-height: 0;
}

.jm-path-card--monogami .jm-path-card__top { background: #2e7d32; }
.jm-path-card--poligami .jm-path-card__top { background: #6b8e23; }

.jm-path-card__icon {
    width: 82px;
    height: 82px;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.jm-path-card__icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.jm-path-card--monogami .jm-path-card__icon img { filter: brightness(0) invert(1); }
.jm-path-card--poligami .jm-path-card__icon img { filter: none; }
.jm-path-card__title { font-size: 1.1rem; font-weight: 700; }

.jm-path-card__indicator {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    border: 2px solid rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s ease, background 0.2s ease;
}
.jm-path-radio:checked + .jm-path-card .jm-path-card__indicator {
    opacity: 1;
    background: #fff;
    border-color: #fff;
}
.jm-path-radio:checked + .jm-path-card .jm-path-card__indicator::after {
    content: '';
    width: 8px;
    height: 12px;
    border: solid #2e7d32;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
    margin-bottom: 3px;
}
.jm-path-radio:checked + .jm-path-card--poligami .jm-path-card__indicator::after {
    border-color: #6b8e23;
}

/* Tablet: saiz sederhana */
@media (min-width: 576px) and (max-width: 991.98px) {
    .jm-path-wrap { max-width: 100%; }
    .jm-path-card__top { height: 130px; min-height: 130px; padding: 1rem 0.6rem 0.75rem; }
    .jm-path-card__icon { width: 64px; height: 64px; }
    .jm-path-card__title { font-size: 1.05rem; }
    .jm-path-card__bottom { padding: 0.65rem 0.4rem; font-size: 0.8rem; }
    .jm-path-card__indicator { width: 22px; height: 22px; top: 8px; right: 8px; }
}

/* Mobile: padankan saiz skrin, seimbang & kemas */
@media (max-width: 575.98px) {
    .jm-path-wrap { max-width: 100%; padding: 0 2px; }
    .jm-path-card { min-height: 180px; border-radius: 14px; }
    .jm-path-card__top {
        height: 110px;
        min-height: 110px;
        padding: 0.6rem 0.4rem 0.5rem;
    }
    .jm-path-card__bottom {
        padding: 0.6rem 0.35rem;
        font-size: 0.72rem;
        line-height: 1.25;
    }
    .jm-path-card__icon {
        width: 48px;
        height: 48px;
        margin-bottom: 0.35rem;
    }
    .jm-path-card__title { font-size: 0.95rem; }
    .jm-path-card__indicator {
        top: 6px;
        right: 6px;
        width: 18px;
        height: 18px;
        border-width: 1.5px;
    }
    .jm-path-radio:checked + .jm-path-card .jm-path-card__indicator::after {
        width: 5px;
        height: 9px;
        margin-bottom: 2px;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const form     = document.getElementById("maleStatusForm");
    const hidden   = document.getElementById("marital_status");
    const pathMonogami = document.getElementById("path_monogami");
    const pathPoligami = document.getElementById("path_poligami");
    const statusSection = document.getElementById("statusPerkahwinanSection");
    const stackMono = document.getElementById("maritalStackMonogami");
    const stackPoly = document.getElementById("maritalStackPoligami");
    const oldValue = hidden.getAttribute("data-old-value") || "";
    var consumedOld = false;

    var optionsMonogami = @json($optionsMonogami);
    var optionsPoligami = @json($optionsPoligami);

    function clearMaritalSelection() {
        document.querySelectorAll(".jm-status-btn").forEach(function (btn) {
            btn.classList.remove("selected");
            btn.setAttribute("aria-selected", "false");
        });
        hidden.value = "";
    }

    function applyMaritalValue(val) {
        clearMaritalSelection();
        if (!val) {
            return;
        }
        var btn = document.querySelector('.jm-status-btn[data-value="' + val + '"]');
        if (btn) {
            btn.classList.add("selected");
            btn.setAttribute("aria-selected", "true");
        }
        hidden.value = val;
    }

    function showStatusStack(path) {
        stackMono.classList.add("d-none");
        stackPoly.classList.add("d-none");
        clearMaritalSelection();
        if (path === "monogami") {
            stackMono.classList.remove("d-none");
            if (!consumedOld && oldValue && optionsMonogami[oldValue]) {
                applyMaritalValue(oldValue);
                consumedOld = true;
            } else {
                hidden.value = "";
            }
        } else if (path === "poligami") {
            stackPoly.classList.remove("d-none");
            if (!consumedOld && oldValue && optionsPoligami[oldValue]) {
                applyMaritalValue(oldValue);
                consumedOld = true;
            } else {
                hidden.value = "";
            }
        }
    }

    var statusSubmitting = false;
    document.querySelectorAll(".jm-status-btn").forEach(function (btn) {
        btn.addEventListener("click", function () {
            if (statusSubmitting) return;
            var val = this.getAttribute("data-value");
            applyMaritalValue(val);
            if (!val || !hidden.value) return;
            statusSubmitting = true;
            document.querySelectorAll(".jm-status-btn").forEach(function (b) { b.disabled = true; });
            form.submit();
        });
    });

    pathMonogami.addEventListener("change", function() {
        if (this.checked) {
            statusSection.style.display = "block";
            showStatusStack("monogami");
        } else if (!pathPoligami.checked) {
            statusSection.style.display = "none";
            clearMaritalSelection();
        }
    });
    pathPoligami.addEventListener("change", function() {
        if (this.checked) {
            statusSection.style.display = "block";
            showStatusStack("poligami");
        } else if (!pathMonogami.checked) {
            statusSection.style.display = "none";
            clearMaritalSelection();
        }
    });

    if (pathMonogami.checked) {
        statusSection.style.display = "block";
        showStatusStack("monogami");
    } else if (pathPoligami.checked) {
        statusSection.style.display = "block";
        showStatusStack("poligami");
    } else {
        statusSection.style.display = "none";
    }
});
</script>
@endpush
