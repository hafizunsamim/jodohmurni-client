        @if (!request()->routeIs('landing')
            && !request()->routeIs('keahlian.info')
            && !request()->routeIs('membership.education')
            && !request()->routeIs('affiliate.public')
            && !request()->routeIs('affiliate.external.dashboard'))
        </div> {{-- .container --}}
        @endif
    </div> {{-- .site_content --}}

    <!-- Scripts asas template -->
    <script src="{{ asset('assets/javascript/jquery.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/javascript/datepicker.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/javascript/bootstrap.min.js') }}?v={{ time() }}"></script>
    {{-- <script src="{{ asset('assets/javascript/tinder.js') }}?v={{ time() }}"></script> --}}
    <script src="{{ asset('assets/javascript/script.js') }}?v={{ time() }}"></script>

    {{-- Axios local --}}
    <script src="{{ asset('assets/javascript/vendor/axios.min.js') }}?v={{ time() }}"></script>
    <script>
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (tokenMeta) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.content;
        }
    </script>

    {{-- Pusher + Laravel Echo dari local --}}
    <script src="{{ asset('assets/javascript/vendor/pusher.min.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/javascript/vendor/echo.iife.js') }}?v={{ time() }}"></script>

    {{-- Init global window.Echo untuk Pusher Channels --}}
    <script>
        window.Pusher = Pusher;

        const EchoModule = window.Echo || window.LaravelEcho;
        const EchoConstructor = EchoModule && EchoModule.default ? EchoModule.default : EchoModule;

        if (!EchoConstructor) {
            console.error((window.jmT ? window.jmT('footer_echo_missing', 'Echo constructor not found. Check echo.iife.js path.') : 'Echo constructor not found. Check echo.iife.js path.'));
        } else {
            window.Echo = new EchoConstructor({
                broadcaster: 'pusher',
                key: '{{ env('PUSHER_APP_KEY') }}',
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                forceTLS: true,

                authEndpoint: '{{ url("/broadcasting/auth") }}',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                },
            });
        }
    </script>

    {{-- 🔔 Global notification listener untuk semua page (desktop / browser biasa) --}}
    @auth
    <script>
    document.addEventListener('DOMContentLoaded', function () {
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

        if (!window.Echo) {
            console.warn(jmT('footer_echo_not_available', 'Echo not available for global notifications'));
            return;
        }

        const currentUserId = @json(Auth::id());
        console.log('Global notify init for user', currentUserId);

        // Minta permission sekali je (kalau belum)
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().catch(() => {});
        }

        // Subscribe ke channel user.{id}
        window.Echo.private('user.' + currentUserId)
            .listen('.message.sent', (e) => {
                console.log('GLOBAL MESSAGE EVENT:', e);

                const msg = e.message || {};
                const senderName = msg.sender_name || jmT('footer_user', 'User');

                if ('Notification' in window && Notification.permission === 'granted') {
                    try {
                        new Notification(jmT('footer_new_message_from', 'New message from ') + senderName, {
                            body: msg.body || '',
                        });
                    } catch (err) {
                        console.warn(jmT('footer_notification_failed', 'Failed to create global notification:'), err);
                    }
                } else {
                    console.log(jmT('footer_notification_not_shown', 'Notification not shown. Permission ='), Notification.permission);
                }
            });
    });
    </script>
    @endauth

    @php($gaQueued = session('ga4_client_events'))
    @if(config('analytics.google_measurement_id') && ! empty($gaQueued) && is_array($gaQueued))
    <script>
    (function () {
        var events = @json($gaQueued);
        if (typeof gtag !== 'function') return;
        events.forEach(function (ev) {
            if (!ev || !ev.name) return;
            gtag('event', ev.name, ev.params || {});
        });
    })();
    </script>
    @endif

    {{-- ✅ main.js load sekali, letak paling bawah (lepas bootstrap, jquery etc) --}}
    <script src="{{ asset('assets/js/main.js') }}?v={{ time() }}"></script>

    @stack('scripts')

    <script>
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("/sw.js")
        .then(function () {
            console.log("SW registered");
        });
    }
    </script>
</body>
</html>
