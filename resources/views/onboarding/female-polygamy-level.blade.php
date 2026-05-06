<link rel="stylesheet" href="{{ asset('css/onboarding.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@extends('layouts.app')

@section('content')

<div class="onboarding-wrapper">
    <div class="onboarding-card animate-in jm-polygamy-card">
        <h3 class="mb-2" style="font-weight:700; color:#1a1a1a;" data-translate="onb_fpoly_level_title">Model Poligami Pilihan</h3>
        <p class="text-muted mb-3" style="font-size:0.95rem;" data-translate="onb_fpoly_level_subtitle">Pilih bentuk poligami yang paling sesuai dengan anda.</p>

        <form id="polyForm" method="POST" action="{{ url('/onboarding/female/polygamy-level') }}">
            @csrf
            <input type="hidden" name="poligami_level" id="polyLevel" value="" required>

            <div class="jm-poly-options">
                <div class="jm-poly-card jm-poly-card--green" data-value="1" role="button" tabindex="0" data-translate-aria-label="onb_fpoly_m1_aria" aria-label="Maklum dan Telus">
                    <div class="jm-poly-card__inner">
                        <div class="jm-poly-card__icon jm-poly-card__icon--green">
                            <img src="{{ asset('assets/images/Wanita-Model-1.png') }}" alt="Model 1: Maklum & Telus" class="jm-poly-card__img">
                        </div>
                        <div class="jm-poly-card__text">
                            <div class="jm-poly-card__title" data-translate="onb_fpoly_m1_title">Model 1: Maklum &amp; Telus</div>
                            <div class="jm-poly-card__desc" data-translate="onb_fpoly_m1_desc">Calon suami telah memaklumkan kepada isteri sedia ada.</div>
                        </div>
                        <div class="jm-poly-card__check"><i class="bi bi-check-circle-fill"></i></div>
                    </div>
                </div>

                <div class="jm-poly-card jm-poly-card--amber" data-value="2" role="button" tabindex="0" data-translate-aria-label="onb_fpoly_m2_aria" aria-label="Terbuka">
                    <div class="jm-poly-card__inner">
                        <div class="jm-poly-card__icon jm-poly-card__icon--amber">
                            <img src="{{ asset('assets/images/Wanita-Model-2.png') }}" alt="Model 2: Terbuka" class="jm-poly-card__img">
                        </div>
                        <div class="jm-poly-card__text">
                            <div class="jm-poly-card__title" data-translate="onb_fpoly_m2_title">Model 2: Terbuka</div>
                            <div class="jm-poly-card__desc" data-translate="onb_fpoly_m2_desc">Terbuka sama ada telah dimaklumkan isteri atau belum.</div>
                        </div>
                        <div class="jm-poly-card__check"><i class="bi bi-check-circle-fill"></i></div>
                    </div>
                </div>

                <div class="jm-poly-card jm-poly-card--red" data-value="3" role="button" tabindex="0" data-translate-aria-label="onb_fpoly_m3_aria" aria-label="Sulit">
                    <div class="jm-poly-card__inner">
                        <div class="jm-poly-card__icon jm-poly-card__icon--red">
                            <img src="{{ asset('assets/images/Wanita-Model-3.png') }}" alt="Model 3: Sulit" class="jm-poly-card__img">
                        </div>
                        <div class="jm-poly-card__text">
                            <div class="jm-poly-card__title" data-translate="onb_fpoly_m3_title">Model 3: Sulit</div>
                            <div class="jm-poly-card__desc" data-translate="onb_fpoly_m3_desc">Tidak berhasrat memaklumkan kepada isteri buat masa ini.</div>
                        </div>
                        <div class="jm-poly-card__check"><i class="bi bi-check-circle-fill"></i></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.jm-polygamy-card { max-width: 520px; margin: 0 auto; }
.jm-poly-options { display: flex; flex-direction: column; gap: 14px; }

.jm-poly-card {
  --glow: rgba(46, 125, 50, 0.55);
  border-radius: 16px;
  padding: 0;
  cursor: pointer;
  position: relative;
  transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
  border: 1px solid rgba(16, 24, 40, 0.10);
  overflow: hidden;
  background: #fff;
  isolation: isolate;
  box-shadow:
    0 22px 55px rgba(16, 24, 40, 0.10),
    0 6px 18px rgba(16, 24, 40, 0.06),
    0 0 0 1px rgba(255,255,255,0.55),
    0 0 26px rgba(46, 125, 50, 0.10); /* glow halus (default) */
}
.jm-poly-card::before {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    radial-gradient(120% 80% at 20% 0%, rgba(255,255,255,0.65) 0%, rgba(255,255,255,0.18) 45%, rgba(255,255,255,0) 70%);
  opacity: 0.9;
  z-index: 0;
}
.jm-poly-card::after {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
  background: transparent;
  opacity: 0;
}
.jm-poly-card__inner {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 16px;
  position: relative;
  z-index: 1;
}
.jm-poly-card__inner::before {
  content: "";
  position: absolute;
  left: 14px;
  right: 14px;
  top: 12px;
  height: 1px;
  background: rgba(255,255,255,0.75);
  opacity: 0.55;
  pointer-events: none;
}
.jm-poly-card__icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.85), 0 6px 14px rgba(16,24,40,0.08);
}
.jm-poly-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.jm-poly-card__text { flex: 1; min-width: 0; }
.jm-poly-card__title { font-weight: 800; font-size: 1.05rem; color: #1a1a1a; margin-bottom: 4px; }
.jm-poly-card__desc { font-size: 0.9rem; color: #4a5568; line-height: 1.35; }
.jm-poly-card__check {
  width: 34px;
  height: 34px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: #0a7e3e;
  background: rgba(10,126,62,0.12);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.8), 0 10px 20px rgba(16,24,40,0.10);
  opacity: 0;
  transform: scale(0.95);
  transition: opacity 0.18s ease, transform 0.18s ease;
}

.jm-poly-card:hover {
  transform: translateY(-1px);
  box-shadow:
    0 30px 70px rgba(16, 24, 40, 0.12),
    0 8px 20px rgba(16, 24, 40, 0.06),
    0 0 0 1px rgba(255,255,255,0.65),
    0 0 34px var(--glow); /* glow jelas bila hover */
}
.jm-poly-card.selected {
  border-color: rgba(46,125,50,0.60);
  box-shadow:
    0 0 0 3px rgba(46,125,50,0.16),
    0 30px 65px rgba(16, 24, 40, 0.10),
    0 6px 18px rgba(16, 24, 40, 0.06),
    0 0 42px var(--glow); /* glow lebih kuat bila selected */
}
.jm-poly-card.selected .jm-poly-card__check { opacity: 1; transform: scale(1); }
.jm-poly-card.selected::after { opacity: 0; }

/* Themes per card */
.jm-poly-card--green {
  --glow: rgba(46, 125, 50, 0.55);
  background:
    radial-gradient(110% 90% at 0% 0%, rgba(147, 255, 218, 0.55) 0%, rgba(255,255,255,0.85) 55%),
    linear-gradient(135deg, rgba(10,126,62,0.10), rgba(247,255,252,0.92));
}
.jm-poly-card--amber {
  --glow: rgba(154, 101, 0, 0.50);
  background:
    radial-gradient(110% 90% at 0% 0%, rgba(255, 231, 176, 0.68) 0%, rgba(255,255,255,0.88) 58%),
    linear-gradient(135deg, rgba(154, 101, 0, 0.10), rgba(255,255,255,0.92));
}
.jm-poly-card--red {
  --glow: rgba(197, 48, 48, 0.45);
  background:
    radial-gradient(110% 90% at 0% 0%, rgba(255, 206, 206, 0.70) 0%, rgba(255,255,255,0.88) 58%),
    linear-gradient(135deg, rgba(197, 48, 48, 0.10), rgba(255,255,255,0.92));
}

.jm-poly-card__icon--green { background: rgba(46,125,50,0.14); color: #1f6f2a; }
.jm-poly-card__icon--amber { background: rgba(201, 125, 0, 0.16); color: #7a5200; }
.jm-poly-card__icon--red { background: rgba(197, 48, 48, 0.14); color: #a61b1b; }

@media (max-width: 575px) {
  .jm-polygamy-card.onboarding-card { padding: 16px 14px; border-radius: 12px; }
  .jm-poly-options { gap: 12px; }
  .jm-poly-card__inner { padding: 14px 14px; gap: 12px; }
  .jm-poly-card__icon { width: 36px; height: 36px; border-radius: 9px; }
  .jm-poly-card__title { font-size: 0.98rem; }
  .jm-poly-card__desc { font-size: 0.83rem; }
  .jm-poly-card__check { width: 32px; height: 32px; }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('polyForm');
  const input = document.getElementById('polyLevel');
  const cards = document.querySelectorAll('.jm-poly-card');

  function submit(value, selectedCard) {
    cards.forEach(c => c.classList.remove('selected'));
    if (selectedCard) selectedCard.classList.add('selected');
    input.value = value;
    form.submit();
  }

  cards.forEach(function(card) {
    card.addEventListener('click', function() {
      submit(this.getAttribute('data-value'), this);
    });
    card.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.click();
      }
    });
  });
});
</script>
@endpush

@endsection
