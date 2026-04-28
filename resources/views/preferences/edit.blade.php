@extends('layouts.app')

@section('content')
<style>
    /* Main Container Styling */
    .section-main {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Inter', sans-serif;
    }

    /* Modern Range Slider Styling */
    .slider-container {
        position: relative;
        width: 100%;
        height: 60px;
        margin-top: 25px;
    }
    .slider-track {
        width: 100%;
        height: 6px;
        background-color: #e5e7eb;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        border-radius: 10px;
    }
    input[type="range"] {
        position: absolute;
        width: 100%;
        background: none;
        pointer-events: none;
        appearance: none;
        -webkit-appearance: none;
        top: 50%;
        transform: translateY(-50%);
    }
    input[type="range"]::-webkit-slider-thumb {
        height: 24px;
        width: 24px;
        border-radius: 50%;
        background: #2e7d32;
        pointer-events: auto;
        -webkit-appearance: none;
        cursor: pointer;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        transition: transform 0.1s ease;
    }
    input[type="range"]::-webkit-slider-thumb:active {
        transform: scale(1.2);
    }
    .age-display {
        display: flex;
        justify-content: space-between;
        font-weight: 700;
        color: #2e7d32;
        font-size: 1.1rem;
    }

    /* Select Dropdown Styling */
    .dropdown-select {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        border: 2px solid #f3f4f6;
        background-color: #f9fafb;
        transition: border-color 0.3s ease;
        appearance: none;
    }
    .dropdown-select:focus {
        border-color: #2e7d32;
        outline: none;
    }

    /* Interactive Buttons */
    .splash-btns-bottom {
        display: flex;
        gap: 12px;
        margin-top: 40px;
    }
    .next-btn, .skip-btn {
        flex: 1;
        padding: 16px;
        border-radius: 15px;
        font-weight: 600;
        font-size: 16px;
        border: none;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Simpan Button (Primary) */
    .next-btn {
        background: linear-gradient(135deg, #2e7d32 0%, #55f15d 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(12, 205, 144, 0.4);
    }
    .next-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(12, 205, 144, 0.4);
    }
    .next-btn:active {
        transform: translateY(0);
    }

    /* Reset Button (Secondary) */
    .skip-btn {
        background-color: #f3f4f6;
        color: #6b7280;
    }
    .skip-btn:hover {
        background-color: #e5e7eb;
    }

    /* Loading Spinner */
    .loading-spinner {
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        margin-right: 10px;
        display: none;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<section class="section-main mb-4">

    <form method="POST" action="{{ route('preferences.update') }}" id="filterForm">
        @csrf
        @method('PUT')

        <div class="inpt-la-main mt-4">
            <h2 class="sign-text-nam" style="font-size: 0.9rem; color: #999; letter-spacing: 1px;">UMUR CALON</h2>
            <div class="age-display">
                <span><span id="min-age-val">{{ old('age_min', $pref->age_min ?? 25) }}</span> Thn</span>
                <span><span id="max-age-val">{{ old('age_max', $pref->age_max ?? 35) }}</span> Thn</span>
            </div>
            <div class="slider-container">
                <div class="slider-track" id="track"></div>
                <input type="range" id="age-min-slider" name="age_min" min="18" max="70"
                       value="{{ old('age_min', $pref->age_min ?? 25) }}">
                <input type="range" id="age-max-slider" name="age_max" min="18" max="70"
                       value="{{ old('age_max', $pref->age_max ?? 35) }}">
            </div>
        </div>
        
        <p class="issAbout pb-3 pt-3" style="text-align: start; color: #666;">Tetapkan 200km jika anda terbuka soal jarak - lebih banyak calon akan dipaparkan.</p>

        <div class="inpt-la-main mt-4">
            <h2 class="sign-text-nam" style="font-size: 0.9rem; color: #999;">RADIUS LOKASI</h2>
            <div class="age-display" style="justify-content: flex-start; gap: 8px;">
                <span><span id="radiusLabel"></span> KM</span>
            </div>

            @php
                $radiusOld = old('location_radius', $pref->location_radius ?? '50km');
                $radiusDefault = 50;
                if ($radiusOld === 'tak_kisah') $radiusDefault = 0;
                elseif ($radiusOld === '51-150km') $radiusDefault = 150;
                elseif ($radiusOld === '151km_ke_atas') $radiusDefault = 200;
                else $radiusDefault = 50;
            @endphp

            <div class="slider-container" style="height: 56px;">
                <div class="slider-track" id="radiusTrack"></div>
                <input type="range"
                       id="radiusRange"
                       min="0"
                       max="200"
                       step="1"
                       value="{{ $radiusDefault }}">
            </div>

            <input type="hidden" name="location_radius" id="radiusInput" value="{{ $radiusOld }}" required>
        </div>

        @if($user->gender === 'female')
        <div class="inpt-la-main mt-4">
            <h2 class="sign-text-nam" style="font-size: 0.9rem; color: #999;">BERSEDIA BERPINDAH?</h2>
            <div class="sign-input-main">
                <select name="willing_to_relocate" class="dropdown-select ps-3" required>
                    <option value="ya" {{ (old('willing_to_relocate', $pref->willing_to_relocate ?? '') === 'ya') ? 'selected' : '' }}>Sanggup Pindah</option>
                    <option value="tidak" {{ (old('willing_to_relocate', $pref->willing_to_relocate ?? '') === 'tidak') ? 'selected' : '' }}>Tidak Sanggup</option>
                    <option value="boleh_dipertimbangkan" {{ (old('willing_to_relocate', $pref->willing_to_relocate ?? '') === 'boleh_dipertimbangkan') ? 'selected' : '' }}>Boleh Dipertimbangkan</option>
                </select>
            </div>
        </div>
        @endif

        <div class="onbording-btn-main splash-btns-bottom">
            <button type="button" class="skip-btn" onclick="handleReset()">
                Reset
            </button>
            <button type="submit" class="next-btn" id="submitBtn">
                <div class="loading-spinner" id="btnSpinner"></div>
                <span id="btnText">Simpan</span>
            </button>
        </div>
    </form>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const minSlider = document.getElementById('age-min-slider');
        const maxSlider = document.getElementById('age-max-slider');
        const minLabel = document.getElementById('min-age-val');
        const maxLabel = document.getElementById('max-age-val');
        const track = document.getElementById('track');
        const form = document.getElementById('filterForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');

        // Function to color the space between two range handles
        function setTrack() {
            const val1 = parseInt(minSlider.value);
            const val2 = parseInt(maxSlider.value);
            const min = parseInt(minSlider.min);
            const max = parseInt(minSlider.max);
            
            const percent1 = ((val1 - min) / (max - min)) * 100;
            const percent2 = ((val2 - min) / (max - min)) * 100;
            track.style.background = `linear-gradient(to right, #e5e7eb ${percent1}%, #2e7d32 ${percent1}%, #2e7d32 ${percent2}%, #e5e7eb ${percent2}%)`;
        }

        const updateUI = () => {
            minLabel.textContent = minSlider.value;
            maxLabel.textContent = maxSlider.value;
            setTrack();
        };

        minSlider.addEventListener('input', () => {
            if (parseInt(minSlider.value) >= parseInt(maxSlider.value)) {
                minSlider.value = parseInt(maxSlider.value) - 1;
            }
            updateUI();
        });

        maxSlider.addEventListener('input', () => {
            if (parseInt(maxSlider.value) <= parseInt(minSlider.value)) {
                maxSlider.value = parseInt(minSlider.value) + 1;
            }
            updateUI();
        });

        // Handle Form Submission Interaction
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            btnSpinner.style.display = 'block';
            btnText.textContent = 'Menyimpan...';
        });

        // Handle Reset with visual update
        window.handleReset = function() {
            if (confirm('Adakah anda pasti mahu reset semua tetapan?')) {
                form.reset();
                // Short timeout to allow browser to clear values before updating UI
                setTimeout(updateUI, 10);
            }
        };

        // ===== Radius lokasi (range slider) =====
        const radiusRange = document.getElementById('radiusRange');
        const radiusLabel = document.getElementById('radiusLabel');
        const radiusInput = document.getElementById('radiusInput');
        const radiusTrack = document.getElementById('radiusTrack');

        function mapRadius(value) {
            value = parseInt(value);
            if (value === 0) return 'tak_kisah';
            if (value <= 50) return '50km';
            if (value <= 150) return '51-150km';
            return '151km_ke_atas';
        }

        function updateRadius() {
            const value = parseInt(radiusRange.value);
            const maxR = parseInt(radiusRange.max);
            const percent = (value / maxR) * 100;

            radiusLabel.textContent = value;
            radiusInput.value = mapRadius(value);

            radiusTrack.style.background = `linear-gradient(to right, #2e7d32 ${percent}%, #e5e7eb ${percent}%)`;
        }

        radiusRange.addEventListener('input', updateRadius);

        // Initial setup
        updateUI();
        updateRadius();
    });
</script>
@endpush
@endsection