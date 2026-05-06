
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 data-translate="onb_match_title">Ciri-ciri Calon Idaman</h2>
    <p class="text-muted">
        <small data-translate="onb_match_note">Hanya umur dan lokasi calon yang akan dipaparkan pada peringkat awal.</small>
    </p>
    <form method="POST" action="{{ route('onboarding.matching_preferences') }}">
        @csrf

        <!-- Umur Calon Idaman (MIN & MAX) -->
        <div class="row mb-3">
            <div class="col-12">
                <label class="form-label mb-2">
                    <span data-translate="onb_match_age_range">Julat Umur Calon:</span>
                    <strong>
                        <span id="ageMinLabel"></span> – <span id="ageMaxLabel"></span> <span data-translate="onb_years">tahun</span>
                    </strong>
                </label>
        
                <div class="age-range-container">
                    <div class="range-track"></div>
                    <div class="range-fill" id="rangeFill"></div>
        
                    <div class="range-thumb-label" id="minTooltip"></div>
                    <div class="range-thumb-label" id="maxTooltip"></div>
        
                    <input type="range" id="ageMin" min="18" max="100"
                           value="{{ old('age_min', $onb['age_min'] ?? 18) }}">
                    <input type="range" id="ageMax" min="18" max="100"
                           value="{{ old('age_max', $onb['age_max'] ?? 35) }}">
                </div>
        
                <input type="hidden" name="age_min" id="ageMinInput">
                <input type="hidden" name="age_max" id="ageMaxInput">
            </div>
        </div>
        

     <!-- Radius Lokasi Calon -->
<div class="mb-3">
    <label class="form-label mb-2">
        <span data-translate="onb_match_radius">Radius Lokasi Calon:</span>
        <strong>
            <span id="radiusLabel"></span> KM
        </strong>
    </label>

    <div class="age-range-container">
        <div class="range-track"></div>
        <div class="range-fill" id="radiusFill"></div>

        <div class="range-thumb-label" id="radiusTooltip"></div>

        <input type="range"
               id="radiusRange"
               min="0"
               max="200"
               step="1"
               value="{{ old('location_radius', $onb['location_radius'] ?? 50) }}">
    </div>

    <input type="hidden" name="location_radius" id="radiusInput">
</div>


<p class="text-muted">
        <small><b data-translate="onb_match_radius_tip">Tetapkan 200km jika anda terbuka soal jarak - lebih banyak calon akan dipaparkan.</b></small>
    </p>


        <button type="submit" class="whol-main-btn mt-4 w-100 btn-success" data-translate="onb_match_next_register">Seterusnya ke Pendaftaran</button>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const minSlider = document.getElementById('ageMin');
    const maxSlider = document.getElementById('ageMax');

    const minLabel = document.getElementById('ageMinLabel');
    const maxLabel = document.getElementById('ageMaxLabel');

    const minTooltip = document.getElementById('minTooltip');
    const maxTooltip = document.getElementById('maxTooltip');
    const rangeFill = document.getElementById('rangeFill');

    const minInput = document.getElementById('ageMinInput');
    const maxInput = document.getElementById('ageMaxInput');

    const min = parseInt(minSlider.min);
    const max = parseInt(minSlider.max);

    function percent(val) {
        return ((val - min) / (max - min)) * 100;
    }

    function updateRange() {
        let minVal = parseInt(minSlider.value);
        let maxVal = parseInt(maxSlider.value);

        if (minVal >= maxVal) {
            minSlider.value = maxVal - 1;
            minVal = maxVal - 1;
        }

        const minPercent = percent(minVal);
        const maxPercent = percent(maxVal);

        rangeFill.style.left = minPercent + '%';
        rangeFill.style.width = (maxPercent - minPercent) + '%';

        minTooltip.style.left = minPercent + '%';
        maxTooltip.style.left = maxPercent + '%';

        minTooltip.textContent = minVal;
        maxTooltip.textContent = maxVal;

        minLabel.textContent = minVal;
        maxLabel.textContent = maxVal;

        minInput.value = minVal;
        maxInput.value = maxVal;
    }

    minSlider.addEventListener('input', updateRange);
    maxSlider.addEventListener('input', updateRange);

    updateRange();
});

document.addEventListener('DOMContentLoaded', () => {
    const radiusRange  = document.getElementById('radiusRange');
    const radiusLabel  = document.getElementById('radiusLabel');
    const radiusFill   = document.getElementById('radiusFill');
    const radiusInput  = document.getElementById('radiusInput');
    const radiusTip    = document.getElementById('radiusTooltip');

    const max = radiusRange.max;

    function mapRadius(value) {
    value = parseInt(value);

    if (value === 0) return 'tak_kisah';
    if (value <= 50) return '50km';
    if (value <= 150) return '51-150km';
    return '151km_ke_atas';
}

    function updateRadius() {
        const value = radiusRange.value;
    const percent = (value / max) * 100;

    radiusLabel.textContent = value;

    // nak hantar string lama ke backend
    radiusInput.value = mapRadius(value);

    radiusFill.style.width = percent + '%';

    radiusTip.textContent = value + ' KM';
    radiusTip.style.left = `calc(${percent}% - 18px)`;
    }

    

    radiusRange.addEventListener('input', updateRadius);
    updateRadius();
});


</script>
@endpush
