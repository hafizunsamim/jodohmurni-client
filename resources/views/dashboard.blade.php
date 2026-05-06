<link rel="stylesheet" type="text/css" href="{{ asset('css/home.css') }}">
@extends('layouts.app')

@section('content')
<section class="section-main section-main-ver">
    <div class="tinder-container" style="position: relative;">

        <a href="{{ route('preferences.edit') }}" class="filter-btn-main">
            <img src="{{ asset('assets/images/svg/filter-svg.svg') }}" alt="filter">
        </a>

        {{-- ✅ LOCATION STATUS BAR --}}
        <div id="locationStatus"
             style="
                position: absolute;
                top: 16px;
                left: 16px;
                z-index: 9999;
                background: rgba(0,0,0,.55);
                color: #fff;
                padding: 8px 10px;
                border-radius: 10px;
                font-size: 12px;
                backdrop-filter: blur(6px);
                max-width: 280px;
             ">
            <div style="font-weight:600;">📍 <span data-translate="dash_location">Location</span></div>
            <div id="locationStatusText" data-translate="dash_location_checking">Checking…</div>
        </div>

        @if ($candidates->isEmpty())
            <div class="end-message">
                <span data-translate="dash_no_candidates_1">Tiada calon berdekatan.</span><br>
                <span data-translate="dash_no_candidates_2">Sila sesuaikan preferensi anda.</span>
            </div>
        @else
            @foreach ($candidates as $index => $c)
                @php
                    $candidateAge = $c->date_of_birth ? \Carbon\Carbon::parse($c->date_of_birth)->age : null;
                    $photo = $c->photo_1 ? asset($c->photo_1) : asset('images/default.jpg');

                    $distanceKm = null;
                    if ($me->latitude && $me->longitude && $c->latitude && $c->longitude) {
                        $distanceKm = \App\Helpers\LocationHelper::distance(
                            (float) $me->latitude,
                            (float) $c->latitude,
                            (float) $me->longitude,
                            (float) $c->longitude,
                        );
                    }
                @endphp

                <div class="tinder-card"
                     data-index="{{ $index }}"
                     data-user-id="{{ $c->id }}"
                     data-profile-url="{{ route('candidates.show', $c->id) }}"
                     style="background-image:url('{{ $photo }}')">

                    {{-- GRADIENT --}}
                    <div class="card-gradient"></div>

                    {{-- INFO --}}
                    <div class="card-info-overlay">
                        <div class="info-box">
                            @if($c->status_keahlian === 'LITE')
                                <span class="badge bg-secondary mb-1" style="font-size: 0.7rem;" data-translate="dash_unpaid_member">Ahli Belum Berbayar</span>
                            @endif
                            <h3>
                                <a href="{{ route('candidates.show', $c->id) }}">
                                    @php
                                        $displayName = $c->public_id ? ($c->public_id) : \App\Support\JmI18n::t('search_candidate', fallback: 'Calon');
                                        if (!empty($hasActiveSub)) {
                                            $displayName = trim(($c->nickname ?? '') . ' ' . ($c->name ?? ''));
                                            if ($displayName === '') $displayName = $c->name ?? \App\Support\JmI18n::t('search_candidate', fallback: 'Calon');
                                        }
                                    @endphp
                                    {{ $displayName }}
                                </a>
                            </h3>

                            <div class="info-tags">
                                @if ($candidateAge)
                                    <span>♀ {{ $candidateAge }} <span data-translate="cand_years_short">yr</span></span>
                                @endif

                                <span>🏠 {{ ucfirst($c->country ?? '-') }}</span>

                                @if ($distanceKm)
                                    <span>📍 {{ round($distanceKm, 1) }} <span data-translate="cand_km">km</span></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- FLOATING ACTION BUTTONS --}}
                    <div class="floating-actions">
                        <button class="fab-btn fab-dislike" data-action="dislike" type="button">
                            <img src="{{ asset('assets/images/svg/close.svg') }}">
                        </button>

                        <button class="fab-btn fab-like" data-action="like" type="button">
                            <img src="{{ asset('assets/images/svg/like.svg') }}">
                        </button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div id="swipeLimitModal" class="swipe-modal hidden">
        <div class="swipe-modal-content">
            <h3 data-translate="dash_swipe_limit_title">5 kali percubaan Percuma Anda sudah tamat.</h3>
            <p data-translate="dash_swipe_limit_desc">Sila subscribe secara one-off untuk chatting tanpa batas dan semua butiran calon tersedia</p>
            <button id="closeSwipeModal" data-translate="dash_ok">OK</button>
        </div>
    </div>

</section>

<script>
/* ============================================================
   ✅ AUTO UPDATE LOCATION + SHOW STATUS
   ============================================================ */
const CSRF = "{{ csrf_token() }}";
const UPDATE_LOCATION_URL = "{{ route('me.location.update') }}";

let __lastLocationSentAt = 0;
const LOCATION_THROTTLE_MS = 30 * 1000; // hantar max sekali setiap 30s
let __watchId = null;

function jmT(key, fallback) {
    try {
        const translations = (window.JM_TRANSLATIONS && typeof window.JM_TRANSLATIONS === "object") ? window.JM_TRANSLATIONS : {};
        const country = (document.body?.dataset?.country || "MY").trim() || "MY";
        const locale = (document.body?.dataset?.locale || "").trim() || "ms";
        const byCountry = translations[country] || translations["MY"] || {};
        const pack = byCountry[locale] || byCountry["en"] || {};
        return pack[key] || (byCountry["en"] ? byCountry["en"][key] : null) || fallback || key;
    } catch {
        return fallback || key;
    }
}

function setLocationStatus(msg) {
    const el = document.getElementById('locationStatusText');
    if (el) el.textContent = msg;
}

function fmt(n, digits = 5) {
    const x = Number(n);
    return Number.isFinite(x) ? x.toFixed(digits) : '-';
}

async function sendLocationToServer(lat, lng, accuracy = null, source = 'dashboard') {
    const now = Date.now();
    if (now - __lastLocationSentAt < LOCATION_THROTTLE_MS) {
        setLocationStatus(jmT('dash_loc_updated_recently', 'Updated recently…'));
        return;
    }
    __lastLocationSentAt = now;

    setLocationStatus(jmT('dash_loc_sending', 'Sending…') + ` (${fmt(lat)}, ${fmt(lng)})`);

    try {
        const res = await fetch(UPDATE_LOCATION_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                accuracy: accuracy,
                source: source
            })
        });

        // kalau 419/401/500 etc.
        if (!res.ok) {
            setLocationStatus(jmT('dash_loc_failed', 'Failed') + ` (${res.status})`);
            return;
        }

        const json = await res.json().catch(() => null);

        if (json && json.ok) {
            const t = new Date().toLocaleTimeString();
            setLocationStatus(jmT('dash_loc_updated', 'Updated') + ` ✅ ${fmt(json.latitude)}, ${fmt(json.longitude)} @ ${t}`);
        } else {
            setLocationStatus(jmT('dash_loc_failed_invalid', 'Failed (invalid response)'));
        }
    } catch (err) {
        console.error('❌ update location failed', err);
        setLocationStatus(jmT('dash_loc_failed_network', 'Failed (network error)'));
    }
}

function requestDashboardLocationOnce() {
    if (!navigator.geolocation) {
        setLocationStatus(jmT('dash_loc_not_supported', 'Geolocation not supported'));
        return;
    }

    setLocationStatus(jmT('dash_loc_requesting', 'Requesting permission…'));

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            setLocationStatus(jmT('dash_loc_got_sending', 'Got location ✅ sending…'));
            sendLocationToServer(
                pos.coords.latitude,
                pos.coords.longitude,
                pos.coords.accuracy ?? null,
                'dashboard:getCurrentPosition'
            );
        },
        (err) => {
            console.warn('⚠️ location denied/unavailable', err);
            if (err.code === 1) setLocationStatus(jmT('dash_loc_permission_denied', 'Permission denied ❌'));
            else if (err.code === 2) setLocationStatus(jmT('dash_loc_unavailable', 'Position unavailable ❌'));
            else if (err.code === 3) setLocationStatus(jmT('dash_loc_timeout', 'Timeout ❌'));
            else setLocationStatus(jmT('dash_loc_error', 'Location error ❌'));
        },
        { enableHighAccuracy: false, timeout: 20000, maximumAge: 0 }
    );
}

// OPTIONAL: kalau kau nak lokasi “sentiasa berubah”
function startDashboardWatchLocation() {
    if (!navigator.geolocation) return;
    if (__watchId !== null) return;

    __watchId = navigator.geolocation.watchPosition(
        (pos) => {
            sendLocationToServer(
                pos.coords.latitude,
                pos.coords.longitude,
                pos.coords.accuracy ?? null,
                'dashboard:watchPosition'
            );
        },
        (err) => {
            console.warn('⚠️ watchPosition error', err);
            setLocationStatus('Watch error ❌');
        },
        { enableHighAccuracy: false, timeout: 60000, maximumAge: 0 }
    );
}

// Run on page load
requestDashboardLocationOnce();
startDashboardWatchLocation();

// Stop watch bila keluar page (elak leak)
window.addEventListener('beforeunload', () => {
    if (__watchId !== null && navigator.geolocation?.clearWatch) {
        navigator.geolocation.clearWatch(__watchId);
        __watchId = null;
    }
});
</script>

<script>
/* ================= CONFIG ================= */
const MAX_SWIPE = 20;
const RESET_AFTER_MS = 10 * 1000; // 10 seconds for testing
const SUBSCRIPTION_URL = "{{ route('subscription.index') }}";
const SWIPE_URL = "{{ route('swipe.store') }}";

/* ================= STATE ================= */
let swipeCount = parseInt(localStorage.getItem('swipeCount') || '0');
let swipeResetAt = parseInt(localStorage.getItem('swipeResetAt') || '0');
let swipeLocked = false;
let resetTimeout = null;
let suppressModalOnLoad = false; // Only suppress on PAGE LOAD, not on swipe attempts

/* ================= SESSION RESTORATION ================= */
// If user navigated back after clicking "OK", suppress modal ON LOAD only
if (sessionStorage.getItem('swipe_modal_acknowledged') === 'true') {
    suppressModalOnLoad = true;
    console.log('ℹ️ Suppressing modal on page load (user came back from subscription)');
}
sessionStorage.removeItem('swipe_modal_acknowledged');

/* ================= CORE RESET FUNCTION ================= */
function resetSwipeLimit() {
    console.log('✅ SWIPE LIMIT RESET TRIGGERED');

    swipeCount = 0;
    swipeResetAt = 0;
    swipeLocked = false;
    suppressModalOnLoad = false; // Reset suppression

    localStorage.removeItem('swipeCount');
    localStorage.removeItem('swipeResetAt');

    const modal = document.getElementById('swipeLimitModal');
    if (modal) modal.classList.add('hidden');

    if (resetTimeout) {
        clearTimeout(resetTimeout);
        resetTimeout = null;
    }

    console.log('🎉 Swipe limit fully reset!');
}

/* ================= PAGE LOAD: HANDLE SCHEDULED RESETS ================= */
const now = Date.now();

if (swipeResetAt && now >= swipeResetAt) {
    console.log(`⏰ Reset time passed. Resetting now.`);
    resetSwipeLimit();
} else if (swipeResetAt) {
    const remaining = swipeResetAt - now;
    console.log(`🕗 Reset scheduled in ${Math.round(remaining/1000)}s`);
    resetTimeout = setTimeout(resetSwipeLimit, remaining);
}

swipeLocked = swipeCount >= MAX_SWIPE;

// Show modal on load ONLY if not suppressed
if (swipeLocked && !suppressModalOnLoad) {
    console.log('⚠️ Showing swipe limit modal on page load');
    document.getElementById('swipeLimitModal').classList.remove('hidden');
} else if (swipeLocked && suppressModalOnLoad) {
    console.log('ℹ️ Swipe locked but suppressing modal on page load');
}

/* ================= MODAL HANDLERS ================= */
function showSwipeLimitPopup() {
    console.log('⚠️ showSwipeLimitPopup() called');

    if (swipeCount < MAX_SWIPE) {
        console.log('   ❌ Not at limit yet');
        return;
    }

    swipeLocked = true;
    suppressModalOnLoad = false; // Reset suppression when actively showing modal

    document.getElementById('swipeLimitModal').classList.remove('hidden');
    console.log('   ✅ Modal displayed');
}

document.getElementById('closeSwipeModal')?.addEventListener('click', () => {
    console.log('✅ User clicked OK - navigating to subscription');

    // Mark for suppression on NEXT page load (when user comes back)
    sessionStorage.setItem('swipe_modal_acknowledged', 'true');

    document.getElementById('swipeLimitModal').classList.add('fade-out');
    setTimeout(() => window.location.href = SUBSCRIPTION_URL, 300);
});

/* ================= LOCK & SCHEDULE RESET ================= */
function lockSwipeAndScheduleReset() {
    swipeLocked = true;
    localStorage.setItem('swipeCount', swipeCount.toString());
    localStorage.setItem('swipeResetAt', (Date.now() + RESET_AFTER_MS).toString());

    if (resetTimeout) clearTimeout(resetTimeout);
    resetTimeout = setTimeout(resetSwipeLimit, RESET_AFTER_MS);

    console.log(`🔒 Swipe locked. Auto-reset in ${RESET_AFTER_MS/1000}s`);
    showSwipeLimitPopup(); // Always show when actively locking
}

/* ================= API ================= */
async function saveSwipe(targetUserId, action) {
    try {
        const res = await fetch(SWIPE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ target_user_id: targetUserId, action })
        });
        const data = await res.json().catch(() => ({}));
        if (typeof gtag === 'function') {
            if (action === 'like') {
                gtag('event', 'like_profile', { engagement_type: 'swipe_like' });
                if (data && data.matched) {
                    gtag('event', 'match_success', { engagement_type: 'mutual_like' });
                }
            }
        }
    } catch (e) {
        console.error('❌ Swipe API error', e);
    }
}

/* ================= SWIPE LOGIC ================= */
document.querySelectorAll('.tinder-card').forEach(card => {
    let startX = 0, currentX = 0, dragging = false;
    const targetUserId = card.dataset.userId;
    const profileUrl = card.dataset.profileUrl;

    card.addEventListener('mousedown', start);
    card.addEventListener('touchstart', start, { passive: true });

    function start(e) {
        if (e.target.closest('.fab-btn')) return;

        // 🔥 CRITICAL: Check limit BEFORE suppressing
        if (swipeCount >= MAX_SWIPE) {
            console.log('✋ Swipe blocked - showing popup');
            showSwipeLimitPopup(); // show even if suppressModalOnLoad is true
            return;
        }

        dragging = true;
        startX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
        card.style.transition = 'none';

        document.addEventListener('mousemove', move);
        document.addEventListener('touchmove', move, { passive: true });
        document.addEventListener('mouseup', end);
        document.addEventListener('touchend', end);
    }

    function move(e) {
        if (!dragging) return;
        const x = e.type === 'mousemove' ? e.clientX : e.touches[0].clientX;
        currentX = x - startX;
        card.style.transform = `translateX(${currentX}px) rotate(${currentX / 20}deg)`;
    }

    function end() {
        dragging = false;
        card.style.transition = '0.3s ease';

        if (Math.abs(currentX) > 120) {
            const action = currentX > 0 ? 'like' : 'dislike';
            const direction = action === 'like' ? 1 : -1;

            card.style.transform = `translateX(${direction * 1200}px) rotate(${direction * 45}deg)`;
            saveSwipe(targetUserId, action);

            swipeCount++;
            localStorage.setItem('swipeCount', swipeCount.toString());
            setTimeout(() => card.remove(), 300);

            if (swipeCount >= MAX_SWIPE) lockSwipeAndScheduleReset();
        } else {
            card.style.transform = 'translateX(0)';
        }

        cleanup();
        currentX = 0;
    }

    function cleanup() {
        document.removeEventListener('mousemove', move);
        document.removeEventListener('touchmove', move);
        document.removeEventListener('mouseup', end);
        document.removeEventListener('touchend', end);
    }

    // Like button
    card.querySelector('.fab-like')?.addEventListener('click', e => {
        e.preventDefault(); e.stopPropagation();

        if (swipeCount >= MAX_SWIPE) {
            console.log('✋ Like button blocked - showing popup');
            showSwipeLimitPopup();
            return;
        }

        saveSwipe(targetUserId, 'like');
        swipeCount++;
        localStorage.setItem('swipeCount', swipeCount.toString());

        if (swipeCount >= MAX_SWIPE) {
            lockSwipeAndScheduleReset();
            return;
        }
        window.location.href = profileUrl;
    });

    // Dislike button
    card.querySelector('.fab-dislike')?.addEventListener('click', e => {
        e.preventDefault(); e.stopPropagation();

        if (swipeCount >= MAX_SWIPE) {
            console.log('✋ Dislike button blocked - showing popup');
            showSwipeLimitPopup();
            return;
        }

        saveSwipe(targetUserId, 'dislike');
        swipeCount++;
        localStorage.setItem('swipeCount', swipeCount.toString());

        if (swipeCount >= MAX_SWIPE) lockSwipeAndScheduleReset();

        card.style.transition = '0.3s ease';
        card.style.transform = `translateX(-1200px) rotate(-45deg)`;
        setTimeout(() => card.remove(), 300);
    });
});

// Cleanup on navigation
window.addEventListener('beforeunload', () => {
    if (resetTimeout) clearTimeout(resetTimeout);
});
</script>

@include('partials.footer-nav', ['active' => 'dashboard'])
@endsection
