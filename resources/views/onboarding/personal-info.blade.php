@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<section class="section-main section-main-ver">
    <h3 class="mb-2 fw-bold" style="color:#2e7d32;">Maklumat Peribadi</h3>
    <p class="into into-sub">Sila lengkapkan maklumat anda.</p>

    <form id="personalInfoForm" method="POST" action="{{ route('onboarding.personal_info') }}">
        @csrf

        {{-- ===================== TARIKH LAHIR ===================== --}}
        <div class="inpt-la-main">
            <label for="datepicker" class="sign-text-nam">TARIKH LAHIR</label>
            <div class="sign-input-main">
                <div class="media-icons-lets">
                    <img src="{{ asset('assets/images/svg/calender.svg') }}" alt="calendar">
                </div>
                <input
                    type="text"
                    id="datepicker"
                    name="date_of_birth"
                    placeholder="DD/MM/YYYY"
                    value="{{ old('date_of_birth', $onb['date_of_birth'] ?? '') }}"
                    autocomplete="off"
                    required
                >
            </div>
            @error('date_of_birth')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- ===================== PENDIDIKAN ===================== --}}
        <div class="inpt-la-main">
            <label class="sign-text-nam">PENDIDIKAN</label>
            <div class="sign-input-main">
                <div class="jm-select2-wrap">
                    <span class="jm-select2-icon" aria-hidden="true">
                        <i class="bi bi-mortarboard"></i>
                    </span>
                    <select name="education_level" id="education_level" class="dropdown-select jm-select2" required>
                    <option value="" {{ !($onb['education_level'] ?? null) ? 'selected' : '' }}>-- Pilih --</option>
                    <option value="spm" {{ ($onb['education_level'] ?? null) === 'spm' ? 'selected' : '' }}>SPM & Setara</option>
                    <option value="diploma" {{ ($onb['education_level'] ?? null) === 'diploma' ? 'selected' : '' }}>Diploma & Setara</option>
                    <option value="ijazah_sarjana_muda" {{ ($onb['education_level'] ?? null) === 'ijazah_sarjana_muda' ? 'selected' : '' }}>Ijazah Sarjana Muda & Setara</option>
                    <option value="ijazah_sarjana_master" {{ ($onb['education_level'] ?? null) === 'ijazah_sarjana_master' ? 'selected' : '' }}>Ijazah Sarjana (Master) & Setara</option>
                    <option value="doktor_falsafah" {{ ($onb['education_level'] ?? null) === 'doktor_falsafah' ? 'selected' : '' }}>Doktor Falsafah (PhD) & setara</option>
                </select>
                </div>
            </div>
            @error('education_level')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- ===================== JENIS PEKERJAAN (OPTIONAL) ===================== --}}
        <div class="inpt-la-main">
            <label class="sign-text-nam">JENIS PEKERJAAN</label>
            <div class="sign-input-main">
                <div class="jm-select2-wrap">
                    <span class="jm-select2-icon" aria-hidden="true">
                        <i class="bi bi-briefcase"></i>
                    </span>
                    <select name="occupation_type" id="occupation_type" class="dropdown-select jm-select2">
                        <option value="" {{ !($onb['occupation_type'] ?? null) ? 'selected' : '' }}>-- Pilih --</option>

                        <option value="kerajaan" {{ ($onb['occupation_type'] ?? null) === 'kerajaan' ? 'selected' : '' }}>
                            Kerajaan
                        </option>

                        <option value="swasta" {{ ($onb['occupation_type'] ?? null) === 'swasta' ? 'selected' : '' }}>
                            Swasta
                        </option>

                        <option value="berniaga_usahawan_sendiri" {{ ($onb['occupation_type'] ?? null) === 'berniaga_usahawan_sendiri' ? 'selected' : '' }}>
                            Berniaga / Usahawan / Kerja Sendiri
                        </option>
                    </select>
                </div>
            </div>
            @error('occupation_type')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- ===================== HOBI ===================== --}}
        <div class="mt-4">
            <label class="sign-text-nam">Hobi</label>
            <p class="into into-sub mb-1">
                Sila senaraikan hobi anda.<br>
                Contoh: <strong>memasak, bola, bowling</strong>
            </p>

            <textarea
                name="hobbies_text"
                class="form-control"
                rows="3"
                placeholder="memasak, bola, bowling"
            >{{ old('hobbies_text', $onb['hobbies'] ?? '') }}</textarea>

            @error('hobbies_text')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- ===================== AKTIVITI SOSIAL ===================== --}}
        <div class="mt-4">
            <label class="sign-text-nam">Aktiviti Sosial & Kemasyarakatan</label>
            <p class="into into-sub mb-1">
                Sila senaraikan aktiviti sosial / kemasyarakatan anda.<br>
                Contoh: <strong>program masjid, sukarelawan NGO, persatuan penduduk</strong>
            </p>

            <textarea
                name="social_activities_text"
                class="form-control"
                rows="3"
                placeholder="program masjid, sukarelawan NGO, persatuan penduduk"
            >{{ old('social_activities_text', $onb['social_activities'] ?? '') }}</textarea>

            @error('social_activities_text')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- ===================== LOKASI (HIDDEN) ===================== --}}
        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $onb['latitude'] ?? '') }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $onb['longitude'] ?? '') }}">
        <input type="hidden" id="location_acquired" name="location_acquired" value="0">

        {{-- ===================== STATUS LOKASI ===================== --}}
        <div class="inpt-la-main">
            <label class="sign-text-nam">LOKASI SEMASA</label>
            <div class="sign-input-main" style="flex-direction: column; align-items: flex-start;">
                <div id="location-status" class="alert alert-info w-100">
                    Menunggu akses lokasi...
                </div>
                <p id="coords-display" class="text-muted small mt-1"></p>
            </div>
        </div>
    </form>

    {{-- ===================== BUTANG SUBMIT (LUAR FORM) ===================== --}}
    <div class="onbording-btn-main splash-btns-bottom">
        <button type="button" id="submitBtn" class="whol-main-btn next-btn btn-success" disabled>
            Seterusnya: Ciri Calon Idaman
        </button>
    </div>
</section>

<style>
/* Select2 + icon (selari dengan onboarding/state & district) */
.jm-select2-wrap{ position: relative; width: 100%; }
.jm-select2-icon{
    position: absolute;
    left: 12px;
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
.jm-select2-wrap .select2-container{ width: 100% !important; }
.jm-select2-wrap .select2-container--default .select2-selection--single{
    height: 52px;
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
    height: 52px;
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
/* ===================== DATEPICKER (JQUERY UI) ===================== */
$(function () {
  $("#datepicker").datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "1950:c",
    maxDate: 0,
    defaultDate: "-25y",
    duration: "fast"
  });
});

/* ===================== GEOLOCATION ===================== */
const statusDiv     = document.getElementById('location-status');
const latInput      = document.getElementById('latitude');
const lngInput      = document.getElementById('longitude');
const coordsDisplay = document.getElementById('coords-display');
const submitBtn     = document.getElementById('submitBtn');

let hasValidLocation = false;
let watchId = null;

const GEO_OPTS_FAST = { enableHighAccuracy: false, timeout: 30000, maximumAge: 0 };
const GEO_OPTS_HI   = { enableHighAccuracy: true,  timeout: 45000, maximumAge: 0 };

function setStatus(html, klass) {
  statusDiv.innerHTML = html;
  statusDiv.className = klass;
}

function setLocation(pos) {
  const lat = pos.coords.latitude;
  const lng = pos.coords.longitude;

  latInput.value = lat;
  lngInput.value = lng;
  hasValidLocation = true;

  const acc = (typeof pos.coords.accuracy === "number") ? `${Math.round(pos.coords.accuracy)}m` : "-";
  const ts  = pos.timestamp ? new Date(pos.timestamp).toLocaleString() : "-";

  setStatus("✅ Lokasi dikesan!", "alert alert-success w-100");
  coordsDisplay.innerHTML = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)} (±${acc})<br><small>${ts}</small>`;

  submitBtn.disabled = false;

  if (watchId !== null && navigator.geolocation?.clearWatch) {
    navigator.geolocation.clearWatch(watchId);
    watchId = null;
  }
}

function explainError(err) {
  let msg = "❌ ";
  switch (err.code) {
    case err.PERMISSION_DENIED:
      msg += "Sila izinkan akses lokasi dalam tetapan browser/OS.";
      break;
    case err.POSITION_UNAVAILABLE:
      msg += "Lokasi tidak dapat ditentukan (desktop perlukan Wi-Fi/location services).";
      break;
    case err.TIMEOUT:
      msg += "Masa tamat. Cuba lagi (pastikan Wi-Fi ON / Location ON).";
      break;
    default:
      msg += "Ralat tidak diketahui.";
  }
  return msg;
}

function ensureRetryButton() {
  if (document.getElementById('retryLocationBtn')) return;

  const btn = document.createElement('button');
  btn.type = 'button';
  btn.id = 'retryLocationBtn';
  btn.className = 'btn btn-outline-secondary btn-sm mt-2';
  btn.textContent = 'Cuba Lagi';
  btn.addEventListener('click', () => requestLocation());
  statusDiv.parentElement.appendChild(btn);
}

function stopWatch() {
  if (watchId !== null && navigator.geolocation?.clearWatch) {
    navigator.geolocation.clearWatch(watchId);
    watchId = null;
  }
}

function requestLocation() {
  hasValidLocation = false;
  submitBtn.disabled = true;
  coordsDisplay.innerHTML = "";
  stopWatch();

  if (!navigator.geolocation) {
    setStatus("⚠️ Pelayar ini tidak menyokong lokasi.", "alert alert-danger w-100");
    ensureRetryButton();
    return;
  }

  setStatus("📍 Minta akses lokasi...", "alert alert-info w-100");

  navigator.geolocation.getCurrentPosition(
    (pos) => setLocation(pos),
    (err) => {
      if (err.code === err.TIMEOUT) {
        setStatus("⏳ Lambat dapat lokasi. Cuba mod lebih tepat...", "alert alert-info w-100");

        navigator.geolocation.getCurrentPosition(
          (pos2) => setLocation(pos2),
          (err2) => {
            setStatus("⏳ Masih lambat. Cuba mode pantau lokasi (watch)...", "alert alert-info w-100");

            watchId = navigator.geolocation.watchPosition(
              (pos3) => setLocation(pos3),
              (err3) => {
                setStatus(explainError(err3), "alert alert-warning w-100");
                ensureRetryButton();
                submitBtn.disabled = true;
              },
              { enableHighAccuracy: false, timeout: 60000, maximumAge: 0 }
            );

            setTimeout(() => {
              if (!hasValidLocation) {
                stopWatch();
                setStatus("❌ Masih tak dapat lokasi. Sila ON Wi-Fi & Windows Location, kemudian Cuba Lagi.", "alert alert-warning w-100");
                ensureRetryButton();
              }
            }, 60000);
          },
          GEO_OPTS_HI
        );
        return;
      }

      setStatus(explainError(err), "alert alert-warning w-100");
      ensureRetryButton();
      submitBtn.disabled = true;
    },
    GEO_OPTS_FAST
  );
}

requestLocation();

/* ===================== SUBMIT HANDLER ===================== */
document.getElementById('submitBtn')?.addEventListener('click', function () {
  if (!hasValidLocation) {
    alert("Sila benarkan akses lokasi untuk teruskan.");
    return;
  }
  document.getElementById('personalInfoForm').submit();
});
</script>

<script>
$(function () {
  if (typeof $.fn.select2 !== 'function') return;

  const $ed = $('#education_level');
  const $oc = $('#occupation_type');

  if ($ed.length) {
    $ed.select2({ width: '100%', placeholder: '-- Pilih --', minimumResultsForSearch: 0 });
    $ed.trigger('change');
  }
  if ($oc.length) {
    $oc.select2({ width: '100%', placeholder: '-- Pilih --', allowClear: true, minimumResultsForSearch: 0 });
    $oc.trigger('change');
  }
});
</script>
@endpush

@endsection
