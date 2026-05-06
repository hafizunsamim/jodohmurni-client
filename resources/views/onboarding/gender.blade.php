<link href="{{ asset('css/onboarding.css') }}" rel="stylesheet">

@extends('layouts.app')

@section('content')
<div class="onboarding-wrapper">
  <div class="gender-onboarding-card animate-in">
    <h3 class="gender-title mb-1 text-center" data-translate="onb_gender_title">Siapakah anda?</h3>
    <p class="gender-subtitle text-muted mb-4" data-translate="onb_gender_subtitle">Pilih jantina anda untuk membantu kami memadankan calon yang sesuai.</p>

    <form id="genderForm" method="POST" action="{{ url('/onboarding/gender') }}">
      @csrf
      <div class="row g-2 g-sm-3 justify-content-center gender-row align-items-stretch">
          <div class="col-6 gender-col">
              <button type="button" class="gender-poster-btn" onclick="selectAndSubmit('male')" data-translate-aria-label="onb_gender_pick_male_aria" aria-label="Pilih Lelaki">
                  <img src="{{ asset('assets/images/icon-lelaki.png') }}" alt="Lelaki" class="img-fluid">
              </button>
          </div>

          <div class="col-6 gender-col">
              <button type="button" class="gender-poster-btn" onclick="selectAndSubmit('female')" data-translate-aria-label="onb_gender_pick_female_aria" aria-label="Pilih Wanita">
                  <img src="{{ asset('assets/images/icon-wanita.png') }}" alt="Wanita" class="img-fluid">
              </button>
          </div>
      </div>
    </form>
  </div>
</div>

<style>
.gender-row.gender-row { align-items: stretch; }
.gender-col { display: flex; min-width: 0; }
.gender-col .gender-poster-btn { flex: 1; width: 100%; }

.gender-poster-btn {
    width: 100%;
    border: 0;
    border: 0;
    background: transparent;
    padding: 0;
    cursor: pointer;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 10px 22px rgba(15, 81, 50, 0.12);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.gender-poster-btn:hover{
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(15, 81, 50, 0.16);
}
.gender-poster-btn:focus-visible{
    outline: 3px solid rgba(46,125,50,0.35);
    outline-offset: 4px;
}
.gender-poster-btn img{
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
}

/* Mobile: kekal kiri-kanan, saiz disesuaikan dengan skrin */
@media (max-width: 575.98px) {
    .gender-onboarding-card { padding-left: 0.25rem; padding-right: 0.25rem; }
    .gender-poster-btn { border-radius: 18px; box-shadow: 0 8px 18px rgba(15, 81, 50, 0.12); }
    /* .gender-poster-btn img { aspect-ratio: 1 / 1.26; object-position: 50% 34%; } */
}
</style>

<script>
function selectAndSubmit(gender) {
  var form = document.getElementById('genderForm');
  var input = form.querySelector('input[name="gender"]');
  if (!input) {
    input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'gender';
    form.appendChild(input);
  }
  input.value = gender;
  form.submit();
}
</script>
@endsection
