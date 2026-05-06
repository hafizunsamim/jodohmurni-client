

@extends('layouts.app')

@section('content')
{{-- Override wajib: penuh lebar & 3 kad 1 baris (langsung dalam page supaya pasti terpakai) --}}
<style>
  body[data-page="landing"],
  body[data-page="landing"] .site_content,
  body[data-page="landing"] .landing-wrapper,
  body[data-page="landing"] .after-hero-content {
    max-width: none !important;
    width: 100% !important;
    box-sizing: border-box;
  }
  @media (min-width: 768px) {
    body[data-page="landing"] .feature-cards-row {
      display: flex !important;
      flex-wrap: nowrap !important;
      width: 100% !important;
      margin-left: 0 !important;
      margin-right: 0 !important;
    }
    body[data-page="landing"] .feature-cards-row .feature-col {
      flex: 1 1 0% !important;
      min-width: 0 !important;
      max-width: none !important;
      width: auto !important;
    }
  }

  /* Button Mula Ta'aruf Sekarang: pill, gradient kuning-oren, ikon chat */
  .jm-taruf-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.875rem 1.75rem;
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a1a1a;
    text-decoration: none;
    background: linear-gradient(180deg, #f5d547 0%, #f0b429 50%, #e8a010 100%);
    border: none;
    border-radius: 9999px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }
  .jm-taruf-btn:hover {
    color: #1a1a1a;
    transform: translateY(1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
  }
  .jm-taruf-btn:active {
    transform: translateY(2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }
  .jm-taruf-btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    color: #1a1a1a;
  }
  .jm-taruf-btn-icon svg {
    width: 100%;
    height: 100%;
  }
</style>
<script>
  if (document.body) {
    document.body.dataset.page = "landing";
    document.body.dataset.country = @json($selectedCountry ?? '');
    document.body.dataset.showNotice = @json((bool) session('show_first_time_notice')) ? "1" : "0";
  }
</script>

<div class="landing-wrapper">
  {{-- HERO: Video berpusat --}}
  <section class="landing-hero">
    <div class="video-card" id="heroWrap">
      <div id="heroVideoMount"
           data-src="{{ asset($videoSrc ?? 'assets/videos/JodohMurniMV.mp4') }}">
      </div>
      <div class="video-controls">
        <button class="video-sound-btn" aria-label="Toggle sound" type="button" disabled>🔇</button>
        <!-- <button class="video-fullscreen-btn" aria-label="Fullscreen" type="button" disabled title="Fullscreen">⛶</button> -->
      </div>
    </div>
  </section>

  {{-- KENAPA PILIH JODOHMURNI + FEATURE CARDS --}}
  <div class="after-hero-content">
    <div class="landing-section feature-cards-section">
      <h2 class="text-center fw-bold section-title" data-translate="kenapa">
        Kenapa Pilih JodohMurni?
      </h2>

      <div class="row feature-cards-row justify-content-center">
          <div class="col-12 col-sm-6 col-md-4 feature-col">
            <div class="feature-card text-center">
              <div class="feature-icon feature-icon-img">
                <img src="{{ asset('assets/images/bimbingan-rumahtangga.png') }}" alt="Bimbingan Rumahtangga" />
              </div>
              <h5 class="feature-card-title" data-translate="card_1">Bimbingan Rumahtangga</h5>
              <p class="feature-card-desc" data-translate="card_1_desc">Bukan sekadar cari jodoh. Anda juga diberi e-book percuma panduan membina rumahtangga, baik monogami mahupun poligami</p>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-4 feature-col">
            <div class="feature-card text-center">
              <div class="feature-icon feature-icon-img">
                <img src="{{ asset('assets/images/niat-jelas-dari-awal.png') }}" alt="Niat Jelas Dari Awal" />
              </div>
              <h5 class="feature-card-title" data-translate="card_2">Niat Jelas Dari Awal</h5>
              <p class="feature-card-desc" data-translate="card_2_desc">Monogami atau poligami; semuanya dinyatakan dengan jujur dan terbimbing tanpa perlu menipu</p>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-4 feature-col">
            <div class="feature-card text-center">
              <div class="feature-icon feature-icon-img">
                <img src="{{ asset('assets/images/bayar-sekali-sahaja.png') }}" alt="Bayar Sekali Sahaja" />
              </div>
              <h5 class="feature-card-title" data-translate="card_3">Bayar Sekali Sahaja</h5>
              <p class="feature-card-desc" data-translate="card_3_desc">Tiada caj tersembunyi, tiada upsell. Semua info calon dipaparkan dengan telus</p>
            </div>
          </div>
      </div>
    </div>

    <div class="hero-minimal-content">
      <a href="{{ route('onboarding.country') }}"
         class="jm-taruf-btn"
         data-translate="taruf">
        <span class="jm-taruf-btn-icon" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            <circle cx="8" cy="12" r="1" fill="currentColor"/>
            <circle cx="12.5" cy="12" r="1" fill="currentColor"/>
            <circle cx="17" cy="12" r="1" fill="currentColor"/>
          </svg>
        </span>
        <span class="jm-taruf-btn-text" data-translate="taruf">Mula Ta'aruf Sekarang</span>
      </a>
    </div>
  </div>

    {{-- =========================================================
       MODAL #1: PILIH NEGARA (CARD SAHAJA)
       ========================================================= --}}
    <div class="modal fade" id="countryPickModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">

          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold fs-4" data-translate="pick_country_title">Pilih Negara</h5>
          </div>

          <div class="modal-body">
            <p class="text-muted mb-4" data-translate="pick_country_subtitle">
              Pilih negara yang anda sedang berada. Sistem JodohMurni tidak mendukung jodoh rentas negara.
            </p>

            <div class="row g-3">
              <div class="col-12">
                <button type="button" class="country-card-btn w-100 d-flex align-items-center p-3 rounded-3 bg-white" data-country="MY">
                  <div class="flag-icon me-3">🇲🇾</div>
                  <div class="text-start">
                    <div class="fw-bold mb-0" data-translate="country_MY">Malaysia</div>
                    <div class="text-muted small" data-translate="country_default_MY">Default: English / Bahasa Melayu</div>
                  </div>
                  <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </button>
              </div>

              <div class="col-12">
                <button type="button" class="country-card-btn country-card-btn--disabled w-100 d-flex align-items-center p-3 rounded-3 bg-white" data-country="ID" disabled aria-disabled="true">
                  <div class="flag-icon me-3">🇮🇩</div>
                  <div class="text-start">
                    <div class="fw-bold mb-0" data-translate="country_ID">Indonesia</div>
                    <div class="text-muted small" data-translate="country_default_ID">Default: English / Bahasa Indonesia</div>
                  </div>
                  <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </button>
              </div>

              <div class="col-12">
                <button type="button" class="country-card-btn country-card-btn--disabled w-100 d-flex align-items-center p-3 rounded-3 bg-white" data-country="SG" disabled aria-disabled="true">
                  <div class="flag-icon me-3">🇸🇬</div>
                  <div class="text-start">
                    <div class="fw-bold mb-0" data-translate="country_SG">Singapore</div>
                    <div class="text-muted small" data-translate="country_default_SG">Default: English / Bahasa Melayu</div>
                  </div>
                  <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </button>
              </div>

              <div class="col-12">
                <button type="button" class="country-card-btn country-card-btn--disabled w-100 d-flex align-items-center p-3 rounded-3 bg-white" data-country="BN" disabled aria-disabled="true">
                  <div class="flag-icon me-3">🇧🇳</div>
                  <div class="text-start">
                    <div class="fw-bold mb-0" data-translate="country_BN">Brunei</div>
                    <div class="text-muted small" data-translate="country_default_BN">Default: English / Bahasa Melayu</div>
                  </div>
                  <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </button>
              </div>
            </div>

            <div id="countryPickError" class="alert alert-danger mt-3 py-2 small" style="display:none;" data-translate="country_error">
              ⚠️ Gagal simpan pilihan negara. Sila cuba lagi.
            </div>
          </div>

          <div class="modal-footer border-0 justify-content-center pb-4">
            <small class="text-muted" data-translate="pick_country_footer">Pilihan anda akan disimpan secara automatik.</small>
          </div>

        </div>
      </div>
    </div>

    {{-- =========================================================
       MODAL #2: NOTICE
       ========================================================= --}}
    <div class="modal fade" id="firstTimeModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <div class="modal-header bg-warning">
            <h5 class="modal-title fw-bold" data-translate="popup_title">Perhatian Penting</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <p class="mb-0" data-translate="popup_content">
              Untuk pertama kali mendaftar, anda wajib menggunakan peranti telefon pintar (Android / iOS)
              kerana terdapat gambar yang perlu diambil secara langsung melalui kamera telefon.
            </p>
          </div>

          <div class="modal-footer">
            <button id="btnFaham" type="button" class="btn btn-warning"  data-translate="popup_button">
              Faham
            </button>
          </div>

        </div>
      </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const mount = document.getElementById("heroVideoMount");
  const soundBtn = document.querySelector(".video-sound-btn");
  const fullscreenBtn = document.querySelector(".video-fullscreen-btn");
  const heroWrap = document.getElementById("heroWrap");
  const btnFaham = document.getElementById("btnFaham");
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
  const showNotice = (document.body?.dataset?.showNotice || "0") === "1";

  let heroVideo = null;

  // ✅ FIX overlay grey / page tak boleh click (modal-backdrop sangkut)
  function forceCloseModalArtifacts() {
    // remove all backdrops
    document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());

    // unlock body scroll & interactions
    document.body.classList.remove("modal-open");
    document.body.style.removeProperty("overflow");
    document.body.style.removeProperty("padding-right");

    // hide any modal still "show" (just in case)
    document.querySelectorAll(".modal.show").forEach(modalEl => {
      modalEl.classList.remove("show");
      modalEl.setAttribute("aria-hidden", "true");
      modalEl.style.display = "none";
    });
  }

  function createAndPlayVideo() {
    if (!mount) return;
    if (heroVideo) return; // dah create

    const src = mount.getAttribute("data-src");
    if (!src) return;

    const v = document.createElement("video");
    v.id = "heroVideo";
    v.muted = false;         // default unmute bila buka screen
    v.playsInline = true;
    v.loop = true;
    v.preload = "auto";

    // biar ikut CSS landing kau, tapi letak fallback supaya penuh
    v.style.width = "100%";
    v.style.height = "100%";
    v.style.objectFit = "cover";

    const s = document.createElement("source");
    s.src = src;
    s.type = "video/mp4";
    v.appendChild(s);

    mount.appendChild(v);
    heroVideo = v;

    if (soundBtn) {
      soundBtn.disabled = false;
      soundBtn.textContent = "🔊";
      soundBtn.onclick = () => {
        heroVideo.muted = !heroVideo.muted;
        soundBtn.textContent = heroVideo.muted ? "🔇" : "🔊";
      };
    }

    function isFullscreen() {
      return !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement);
    }
    function updateFullscreenIcon() {
      if (!fullscreenBtn) return;
      fullscreenBtn.textContent = isFullscreen() ? "✕" : "⛶";
      fullscreenBtn.title = isFullscreen() ? "Keluar fullscreen" : "Fullscreen";
    }
    function toggleFullscreen() {
      if (!heroVideo) return;
      try {
        if (isFullscreen()) {
          if (document.exitFullscreen) document.exitFullscreen();
          else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
          else if (document.mozCancelFullScreen) document.mozCancelFullScreen();
        } else {
          if (heroVideo.requestFullscreen) heroVideo.requestFullscreen();
          else if (heroVideo.webkitRequestFullscreen) heroVideo.webkitRequestFullscreen();
          else if (heroVideo.mozRequestFullScreen) heroVideo.mozRequestFullScreen();
        }
      } catch (e) {}
    }
    document.addEventListener("fullscreenchange", updateFullscreenIcon);
    document.addEventListener("webkitfullscreenchange", updateFullscreenIcon);
    document.addEventListener("mozfullscreenchange", updateFullscreenIcon);

    if (fullscreenBtn) {
      fullscreenBtn.disabled = false;
      fullscreenBtn.onclick = toggleFullscreen;
    }

    v.load();
    const p = v.play();
    if (p && typeof p.catch === "function") {
      p.catch(() => {});
    }
  }

if (showNotice) {
  const modalEl = document.getElementById("firstTimeModal");

  if (modalEl && window.bootstrap?.Modal) {
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
      backdrop: true,
      keyboard: true,
    });

    // bila modal betul2 dah hilang, baru play video + unlock (fallback)
    modalEl.addEventListener("hidden.bs.modal", () => {
      forceCloseModalArtifacts(); // optional fallback
      createAndPlayVideo();
    }, { once: true });

    modal.show();

    if (btnFaham) {
      btnFaham.addEventListener("click", () => {
        modal.hide(); // ✅ tutup modal cara bootstrap
      }, { once: true });
    }
  }
} else {
  createAndPlayVideo();
}


  // ==============================
  // COUNTRY PICK (AJAX via fetch)
  // ==============================
  const errorEl = document.getElementById("countryPickError");

  document.querySelectorAll(".country-card-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      if (btn.disabled) return;
      const selectedCountry = btn.getAttribute("data-country");
      if (!selectedCountry) return;

      localStorage.setItem("selected_country", selectedCountry);

      // UI feedback (hanya butang yang enabled)
      document.querySelectorAll(".country-card-btn:not(:disabled)").forEach((b) => { b.style.opacity = "0.5"; });
      btn.style.opacity = "1";

      const chevron = btn.querySelector(".bi-chevron-right");
      if (chevron) chevron.outerHTML = '<span class="spinner-border spinner-border-sm text-primary ms-auto"></span>';
      if (errorEl) errorEl.style.display = "none";

      try {
        const res = await fetch("{{ route('set.country') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({ country: selectedCountry }),
        });

        if (!res.ok) throw new Error(jmT("country_http_error_prefix", "HTTP ") + res.status);
        const data = await res.json().catch(() => ({}));

        if (data && data.ok) {
          window.location.reload();
        } else {
          throw new Error(jmT("country_response_not_ok", "Response not ok"));
        }
      } catch (e) {
        if (errorEl) errorEl.style.display = "block";
        document.querySelectorAll(".country-card-btn:not(:disabled)").forEach((b) => { b.style.opacity = "1"; });
      }
    });
  });
});
</script>

@if(config('analytics.google_measurement_id'))
@push('scripts')
<script>
(function () {
    if (typeof gtag !== 'function') return;
    gtag('event', 'jm_homepage_visit', {
        link_url: 'https://jodohmurni.com/',
        page_location: window.location.href
    });
})();
</script>
@endpush
@endif

@endsection
