/* =========================================================
   JODOHMURNI MAIN.JS
   - Country-based translations (from window.JM_TRANSLATIONS)
   - Language dropdown filtered by country
   - Landing country picker modal + notice modal
   ========================================================= */

/* =========================
   TRANSLATIONS BY COUNTRY
   (Injected from Blade: window.JM_TRANSLATIONS)
   ========================= */
const translations = (window.JM_TRANSLATIONS && typeof window.JM_TRANSLATIONS === "object")
  ? window.JM_TRANSLATIONS
  : {};

/* =========================
   STORAGE KEYS
   ========================= */
const JM_COUNTRY_KEY = "jm_country";            // MY/ID/SG/BN (mirror client)
const JM_LANG_KEY = "jm_lang";                  // en/ms/id
const JM_LANG_MANUAL_KEY = "jm_lang_manual";    // "1" if user manually selects language

function safeGet(k) { try { return localStorage.getItem(k); } catch { return null; } }
function safeSet(k, v) { try { return localStorage.setItem(k, v); } catch {} }
function safeRemove(k) { try { return localStorage.removeItem(k); } catch {} }

/* =========================
   COUNTRY/LANG RULES
   ========================= */
function allowedLangsByCountry(country) {
  // RULE:
  // MY/SG/BN => EN + MS
  // ID       => EN + ID
  if (country === "ID") return ["en", "id"];
  if (country === "MY" || country === "SG" || country === "BN") return ["en", "ms"];
  // belum pilih: bagi semua utk testing
  return ["en", "ms", "id"];
}

function defaultLangFromCountry(country) {
  if (country === "ID") return "id";
  if (country === "MY" || country === "SG" || country === "BN") return "ms";
  return "en";
}

/* =========================
   GET TRANSLATION VALUE
   ========================= */
function t(country, lang, key) {
  const c = country && translations[country] ? country : "MY"; // fallback safe
  const langPack = translations[c]?.[lang] || translations[c]?.en;
  return langPack?.[key] ?? null;
}

/* =========================
   APPLY TRANSLATION TO DOM
   ========================= */
function applyTranslations(country, lang) {
  document.querySelectorAll("[data-translate]").forEach((el) => {
    const key = el.getAttribute("data-translate");
    const val = t(country, lang, key);
    if (!val) return;

    if (key === "popup_content") el.innerHTML = val;
    else el.textContent = val;
  });
}

/* =========================
   FILTER LANGUAGE DROPDOWN
   ========================= */
function applyLanguageOptionsByCountry(country) {
  const allowed = allowedLangsByCountry(country);
  document.querySelectorAll("#langMenu li[data-lang]").forEach((li) => {
    const lang = li.dataset.lang;
    li.style.display = allowed.includes(lang) ? "" : "none";
  });
}

function updateLangDropdownUI(lang) {
  const currentFlag = document.getElementById("currentFlag");
  if (!currentFlag) return;

  const item = document.querySelector(`#langMenu li[data-lang="${lang}"]`);
  const flag = item?.dataset?.flag;
  if (flag) currentFlag.src = flag;
}

/* =========================
   SET LANGUAGE (persist)
   ========================= */
function setLanguage(country, lang, { manual = false } = {}) {
  const allowed = allowedLangsByCountry(country);
  if (!allowed.includes(lang)) {
    lang = allowed.includes(defaultLangFromCountry(country))
      ? defaultLangFromCountry(country)
      : "en";
  }

  applyTranslations(country, lang);
  safeSet(JM_LANG_KEY, lang);

  if (manual) safeSet(JM_LANG_MANUAL_KEY, "1");
  updateLangDropdownUI(lang);
}

/* =========================
   INITIAL LANGUAGE PICK
   ========================= */
function applyInitialLanguage(country) {
  const allowed = allowedLangsByCountry(country);
  const manual = safeGet(JM_LANG_MANUAL_KEY) === "1";
  const storedLang = safeGet(JM_LANG_KEY);

  // 1) manual lock & still allowed
  if (manual && storedLang && allowed.includes(storedLang)) {
    setLanguage(country, storedLang, { manual: false });
    return;
  }

  // 2) default by country
  const def = defaultLangFromCountry(country);
  if (allowed.includes(def)) {
    setLanguage(country, def, { manual: false });
    return;
  }

  // 3) fallback
  setLanguage(country, allowed.includes(storedLang) ? storedLang : "en", { manual: false });
}

/* =========================
   HEADER DROPDOWN INIT
   ========================= */
function initLangDropdown(getCountryFn) {
  const langToggle = document.getElementById("langToggle");
  const langMenu = document.getElementById("langMenu");
  if (!langToggle || !langMenu) return;

  langToggle.addEventListener("click", (e) => {
    e.preventDefault();
    langMenu.classList.toggle("show");
  });

  document.querySelectorAll("#langMenu li[data-lang]").forEach((item) => {
    item.addEventListener("click", () => {
      if (item.style.display === "none") return;

      const country = getCountryFn ? getCountryFn() : (document.body?.dataset?.country || "");
      const lang = item.dataset.lang;

      setLanguage(country, lang, { manual: true });
      langMenu.classList.remove("show");
    });
  });

  document.addEventListener("click", (e) => {
    if (!e.target.closest(".lang-dropdown")) {
      langMenu.classList.remove("show");
    }
  });
}

/* =========================
   LANDING INIT
   ========================= */
function initLanding() {
  const countryModalEl = document.getElementById("countryPickModal");
  if (!countryModalEl) return; // bukan landing

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  // server country from session (blade inject to body.dataset.country)
  const serverCountry = (document.body?.dataset?.country || "").trim();
  const showNotice = document.body?.dataset?.showNotice === "1";

  // 1) filter dropdown options by country
  applyLanguageOptionsByCountry(serverCountry || "");

  // 2) set initial language
  applyInitialLanguage(serverCountry || "");

  // bootstrap modals (bootstrap is loaded in footer before main.js now)
  const countryModal = new bootstrap.Modal(countryModalEl, { backdrop: "static", keyboard: false });
  const noticeModalEl = document.getElementById("firstTimeModal");
  const noticeModal = noticeModalEl ? new bootstrap.Modal(noticeModalEl) : null;

  function openCountryModal() {
    const err = document.getElementById("countryPickError");
    if (err) err.style.display = "none";

    document.querySelectorAll("#countryPickModal .country-card-btn").forEach((b) => {
      b.removeAttribute("disabled");
    });

    countryModal.show();
  }

  // testing button
  const changeCountryBtn = document.getElementById("changeCountryBtn");
  if (changeCountryBtn) {
    changeCountryBtn.addEventListener("click", () => openCountryModal());
  }

  // auto open if not selected / forced
  const params = new URLSearchParams(window.location.search);
  const forcePick = params.get("country") === "1";
  if (forcePick || !serverCountry) openCountryModal();

  // show notice after pick (flash)
  if (showNotice && noticeModal) {
    try { countryModal.hide(); } catch {}
    noticeModal.show();
  }

  // click country card
  document.querySelectorAll("#countryPickModal .country-card-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const code = btn.getAttribute("data-country");
      if (!code) return;

      document.querySelectorAll("#countryPickModal .country-card-btn").forEach((b) => {
        b.setAttribute("disabled", "disabled");
      });

      try {
        const res = await fetch("/landing/set-country", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
            Accept: "application/json",
          },
          body: JSON.stringify({ country: code }),
        });

        if (!res.ok) {
          if (res.status === 419) {
            throw new Error("Sesi tamat (419). Sila refresh halaman (F5).");
          }
          if (res.status >= 500) {
            throw new Error("Ralat pelayan. Sila cuba lagi.");
          }
          throw new Error("Request failed");
        }
        const data = await res.json();
        if (!data.ok) throw new Error("Not ok");

        // mirror country on client for convenience
        safeSet(JM_COUNTRY_KEY, code);

        // bila negara berubah, kalau bahasa manual sebelum ni tak valid -> reset manual lock
        const allowed = allowedLangsByCountry(code);
        const storedLang = safeGet(JM_LANG_KEY);
        if (storedLang && !allowed.includes(storedLang)) safeRemove(JM_LANG_MANUAL_KEY);

        // filter dropdown based on new country
        applyLanguageOptionsByCountry(code);

        // if not manual, set default for new country
        const manual = safeGet(JM_LANG_MANUAL_KEY) === "1";
        if (!manual) setLanguage(code, defaultLangFromCountry(code), { manual: false });

        // reload clean
        window.location.href = "/";
      } catch (e) {
        document.querySelectorAll("#countryPickModal .country-card-btn").forEach((b) => {
          b.removeAttribute("disabled");
        });

        const err = document.getElementById("countryPickError");
        if (err) {
          const curCountry = serverCountry || "MY";
          const curLang = safeGet(JM_LANG_KEY) || defaultLangFromCountry(curCountry);
          err.textContent = e.message || t(curCountry, curLang, "country_error") || "Gagal simpan pilihan negara. Sila cuba lagi.";
          err.style.display = "block";
        }
      }
    });
  });

  // video sound
  const video = document.getElementById("heroVideo");
  const soundBtn = document.querySelector(".video-sound-btn");
  if (video && soundBtn) {
    let soundEnabled = false;

    (async () => {
      try {
        video.muted = true;
        video.volume = 0;
        await video.play();
      } catch {}
    })();

    const enableSoundOnce = async () => {
      if (soundEnabled) return;
      try {
        video.muted = false;
        video.volume = 1;
        await video.play();
        soundBtn.textContent = "🔊";
        soundEnabled = true;
      } catch {}
      window.removeEventListener("click", enableSoundOnce);
      window.removeEventListener("touchstart", enableSoundOnce);
    };

    window.addEventListener("click", enableSoundOnce, { passive: true });
    window.addEventListener("touchstart", enableSoundOnce, { passive: true });

    soundBtn.addEventListener("click", async (e) => {
      e.stopPropagation();
      video.muted = !video.muted;
      video.volume = video.muted ? 0 : 1;
      soundBtn.textContent = video.muted ? "🔇" : "🔊";
      try { await video.play(); } catch {}
    });
  }
}

/* =========================
   BOOT
   ========================= */
document.addEventListener("DOMContentLoaded", () => {
  // get country from body dataset (server session)
  const getCountry = () => (document.body?.dataset?.country || "").trim();

  // init lang dropdown (works for all pages)
  initLangDropdown(getCountry);

  // landing?
  const isLanding = document.body?.dataset?.page === "landing" || !!document.getElementById("countryPickModal");

  if (isLanding) {
    initLanding();
  } else {
    // non-landing: apply stored language (no country modal)
    const country = getCountry() || safeGet(JM_COUNTRY_KEY) || "MY";
    applyLanguageOptionsByCountry(country);

    const storedLang = safeGet(JM_LANG_KEY);
    const allowed = allowedLangsByCountry(country);
    const lang = allowed.includes(storedLang) ? storedLang : defaultLangFromCountry(country);

    setLanguage(country, lang, { manual: false });
  }
});
