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
            console.error('Tak jumpa Echo constructor. Check echo.iife.js path.');
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
        if (!window.Echo) {
            console.warn('Echo not available for global notifications');
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
                const senderName = msg.sender_name || 'Pengguna';

                if ('Notification' in window && Notification.permission === 'granted') {
                    try {
                        new Notification('Mesej baru dari ' + senderName, {
                            body: msg.body || '',
                        });
                    } catch (err) {
                        console.warn('Gagal create global notification:', err);
                    }
                } else {
                    console.log('Notification not shown. Permission =', Notification.permission);
                }
            });
    });
    </script>
    @endauth

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
