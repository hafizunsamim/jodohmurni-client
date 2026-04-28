/* service-worker.js */

// Basic install / activate
self.addEventListener('install', (event) => {
    console.log('[SW] Installed');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('[SW] Activated');
    return self.clients.claim();
});

// Handle push event
self.addEventListener('push', function (event) {
    console.log('[SW] Push received:', event);

    let data = {};
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = { title: 'Mesej baru', body: event.data.text() };
        }
    }

    const title = data.title || 'Mesej baru';
    const options = {
        body: data.body || '',
        data: {
            url: data.url || '/dashboard',
        },
        // icon: '/icons/icon-192x192.png', // kalau ada
        badge: '/assets/images/svg/favicon.svg'
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Klik noti → buka / fokus tab
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.url) || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(windowClients => {
                for (let client of windowClients) {
                    if (client.url.includes(url) && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});
