<link rel="stylesheet" href="{{ asset('css/onboarding.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@extends('layouts.app')

@section('content')

<div class="onboarding-wrapper">
  <div class="onboarding-card animate-in fp-preference-card">
    {{-- Header --}}
    <div class="fp-header">
      <h1 class="fp-title">Keutamaan Jodoh</h1>
      <p class="fp-subtitle">(Wanita)</p>
      <p class="fp-instruction">Pilih matlamat perkahwinan yang paling sesuai dengan diri anda.</p>
      <p class="fp-note text-muted small">Pilihan ini membantu kami padankan anda dengan calon yang sesuai.</p>
    </div>

    <form id="prefForm" method="POST" action="{{ url('/onboarding/female/preference') }}">
      @csrf
      <input type="hidden" name="target" id="targetInput" value="" required>

      <div class="fp-options">
        {{-- Fokus Eksklusif - Monogami sahaja --}}
        <div class="fp-option-card fp-option-card--green" data-value="monogami" role="button" tabindex="0">
          <span class="fp-badge fp-badge--green">Fokus Eksklusif</span>
          <div class="fp-option-body">
            <span class="fp-option-icon fp-option-icon--check"><i class="bi bi-check-circle"></i></span>
            <div class="fp-option-text">
              <strong class="fp-option-title">Monogami sahaja</strong>
              <p class="fp-option-desc mb-0">Saya hanya mempertimbangkan lelaki yang tidak beristeri.</p>
            </div>
            <span class="fp-option-check"><i class="bi bi-check-circle-fill"></i></span>
          </div>
        </div>

        {{-- Fleksibel - Terbuka --}}
        <div class="fp-option-card fp-option-card--blue" data-value="terbuka" role="button" tabindex="0">
          <span class="fp-badge fp-badge--blue">Fleksibel</span>
          <div class="fp-option-body">
            <span class="fp-option-icon fp-option-icon--circle"><i class="bi bi-circle"></i></span>
            <div class="fp-option-text">
              <strong class="fp-option-title">Terbuka (Monogami / Poligami)</strong>
              <p class="fp-option-desc mb-0">Saya terbuka menilai kedua-duanya berdasarkan keserasian.</p>
            </div>
            <span class="fp-option-check"><i class="bi bi-check-circle-fill"></i></span>
          </div>
        </div>

        {{-- Terbimbing - Poligami berprinsip --}}
        <div class="fp-option-card fp-option-card--purple" data-value="poligami" role="button" tabindex="0">
          <span class="fp-badge fp-badge--purple">Terbimbing</span>
          <div class="fp-option-body">
            <span class="fp-option-icon fp-option-icon--people"><i class="bi bi-people"></i></span>
            <div class="fp-option-text">
              <strong class="fp-option-title">Poligami berprinsip</strong>
              <p class="fp-option-desc mb-0">Saya faham dan bersedia ke arah perkahwinan poligami.</p>
            </div>
            <span class="fp-option-check"><i class="bi bi-check-circle-fill"></i></span>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
.fp-preference-card { max-width: 520px; margin: 0 auto; }
.fp-header { margin-bottom: 1.5rem; }
.fp-header-sparkle { font-size: 1.25rem; opacity: 0.9; }
.fp-title { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.15rem; }
.fp-subtitle { font-size: 1rem; font-weight: 400; color: #2d3748; margin-bottom: 0.5rem; }
.fp-instruction { font-size: 0.95rem; color: #2d3748; margin-bottom: 0.35rem; }
.fp-note { font-size: 0.875rem; }

.fp-options { display: flex; flex-direction: column; gap: 1rem; }
.fp-option-card {
  background: #fff;
  border: 2px solid #e2e8f0;
  border-radius: 16px;
  padding: 1rem 1.25rem;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  position: relative;
}
.fp-option-card:hover {
  border-color: #b2d3c2;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.fp-option-card.selected { border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46,125,50,0.2); background: #f8fdf9; }
.fp-badge {
  display: inline-block;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.25rem 0.6rem;
  border-radius: 999px;
  margin-bottom: 0.6rem;
}
.fp-badge--green { background: #d4edda; color: #155724; }
.fp-badge--blue { background: #cce5ff; color: #004085; }
.fp-badge--purple { background: #e2d5f1; color: #4a2c6a; }
.fp-option-body { display: flex; align-items: flex-start; gap: 12px; }
.fp-option-icon { font-size: 1.5rem; flex-shrink: 0; }
.fp-option-icon--check { color: #2e7d32; }
.fp-option-icon--circle { color: #94a3b8; }
.fp-option-icon--people { color: #6b4d8a; }
.fp-option-text { flex: 1; min-width: 0; }
.fp-option-title { display: block; font-size: 1rem; color: #1a1a1a; margin-bottom: 0.25rem; }
.fp-option-desc { font-size: 0.875rem; color: #4a5568; line-height: 1.4; }
.fp-option-check { font-size: 1.25rem; color: #2e7d32; flex-shrink: 0; opacity: 0; transition: opacity 0.2s; }
.fp-option-card.selected .fp-option-check { opacity: 1; }

/* Mobile: lebih kemas dan seimbang */
@media (max-width: 575px) {
  .onboarding-wrapper { margin-top: 16px; padding: 0 12px; min-height: auto; align-items: flex-start; padding-top: 12px; padding-bottom: 24px; }
  .fp-preference-card.onboarding-card { padding: 16px 14px; border-radius: 12px; }
  .fp-header { margin-bottom: 1rem; }
  .fp-title { font-size: 1.25rem; margin-bottom: 0.1rem; }
  .fp-subtitle { font-size: 0.9rem; margin-bottom: 0.25rem; }
  .fp-instruction { font-size: 0.875rem; margin-bottom: 0.2rem; }
  .fp-note { font-size: 0.8rem; margin-bottom: 0; }
  .fp-options { gap: 0.6rem; }
  .fp-option-card {
    padding: 0.65rem 0.85rem;
    border-radius: 12px;
    border-width: 1.5px;
  }
  .fp-badge { font-size: 0.65rem; padding: 0.2rem 0.5rem; margin-bottom: 0.4rem; }
  .fp-option-body { gap: 8px; }
  .fp-option-icon { font-size: 1.25rem; }
  .fp-option-title { font-size: 0.9rem; margin-bottom: 0.15rem; }
  .fp-option-desc { font-size: 0.8rem; line-height: 1.35; }
  .fp-option-check { font-size: 1.1rem; }
}
</style>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("prefForm");
  const targetInput = document.getElementById("targetInput");
  const cards = document.querySelectorAll(".fp-option-card");

  cards.forEach(function (card) {
    card.addEventListener("click", function () {
      var value = this.getAttribute("data-value");
      cards.forEach(function (c) { c.classList.remove("selected"); });
      this.classList.add("selected");
      targetInput.value = value;
      form.submit();
    });
    card.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        this.click();
      }
    });
  });
});
</script>
@endpush

@endsection
