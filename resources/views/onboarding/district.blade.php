<link rel="stylesheet" type="text/css" href="{{ asset('css/onboarding.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

@extends('layouts.app')

@section('content')
<div class="onboarding-wrapper">
    <div class="onboarding-card animate-in">

        <!-- Progress (Step 3 of onboarding) -->
        <div class="progress mb-4" style="height:6px;">
            <div class="progress-bar bg-success" style="width:75%"></div>
        </div>

        <h4 class="fw-bold mb-1">Pilih Daerah Anda</h4>

        <p class="text-muted small mb-1">
            Negara:
            <strong class="text-success">
                {{ ucfirst($onb['country'] ?? '') }}
            </strong>
        </p>

        <p class="text-muted small">
            Negeri:
            <strong class="text-success">
                {{ $onb['state'] ?? '' }}
            </strong>
        </p>

        <form action="{{ route('onboarding.district') }}" method="POST" class="mt-4">
            @csrf

            <div class="mb-3">
                <label for="district_id" class="form-label fw-semibold">
                    Daerah / Area
                </label>

                <div class="jm-select2-wrap">
                    <span class="jm-select2-icon" aria-hidden="true">
                        <i class="bi bi-geo-alt"></i>
                    </span>

                    <select
                        name="district_id"
                        id="district_id"
                        class="form-select form-select-lg interactive-select jm-select2
                        @error('district_id') is-invalid shake @enderror"
                        required
                    >
                        <option value="">-- Pilih satu --</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}"
                                {{ old('district_id', $onb['district_id'] ?? null) == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- <small id="districtPreview"
                       class="text-success d-none mt-2">
                    ✅ Daerah dipilih
                </small> --}}

                @error('district_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between gap-2 mt-4">
                <a href="{{ route('onboarding.state') }}"
                   class="btn btn-outline-secondary btn-sm">
                    ← Tukar Negeri
                </a>

                <button type="submit"
                        id="nextDistrictBtn"
                        class="btn btn-success btn-sm px-4"
                        disabled>
                    Seterusnya →
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.jm-select2-wrap{ position: relative; }
.jm-select2-icon{
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f0f9f4;
    color: #2e7d32;
    border: 1px solid #e2e8f0;
    z-index: 2;
    pointer-events: none;
}

/* Select2 – match dropdown Negeri */
.jm-select2-wrap .select2-container{ width: 100% !important; }
.jm-select2-wrap .select2-container--default .select2-selection--single{
    height: 48px;
    border: 1px solid #b2d3c2;
    border-radius: 12px;
    display: flex;
    align-items: center;
    padding-left: 54px; /* ruang icon */
    background: rgba(255,255,255,0.85);
}
.jm-select2-wrap .select2-container--default .select2-selection--single .select2-selection__rendered{
    padding-left: 0;
    line-height: 1.2;
    color: #2d3748;
}
.jm-select2-wrap .select2-container--default .select2-selection--single .select2-selection__arrow{
    height: 48px;
    right: 10px;
}
.jm-select2-wrap .select2-container--default.select2-container--focus .select2-selection--single{
    border-color: #2e7d32;
    box-shadow: 0 0 0 0.15rem rgba(46,125,50,.25);
}
.jm-select2-wrap .select2-dropdown{
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 14px 30px rgba(0,0,0,0.10);
}
.jm-select2-wrap .select2-results__option{ padding: 10px 12px; }
.jm-select2-wrap .select2-results__option--highlighted.select2-results__option--selectable{
    background: #e8f5f0;
    color: #0a7e3e;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("district_id");
    const nextBtn = document.getElementById("nextDistrictBtn");
    // const preview = document.getElementById("districtPreview");

    if (!select) return;

    function updateUI() {
        if (select.value) {
            nextBtn.disabled = false;
            // preview.classList.remove("d-none");
            // preview.textContent =
            //     "✅ Daerah dipilih: " + select.options[select.selectedIndex].text;
        } else {
            nextBtn.disabled = true;
            // preview.classList.add("d-none");
        }
    }

    updateUI();
    select.addEventListener("change", updateUI);

    // Select2 triggers change via jQuery; listen to both
    if (window.jQuery) {
        $(select).on("change change.select2", updateUI);
    }
});
</script>

<script>
$(function () {
    const $sel = $('#district_id');
    if (!$sel.length || typeof $sel.select2 !== 'function') return;

    $sel.select2({
        width: '100%',
        placeholder: '-- Pilih satu --',
        minimumResultsForSearch: 0
    });

    // re-check state after Select2 initializes / sets value
    $sel.trigger('change');
});
</script>
@endpush
