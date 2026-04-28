# Chat Real-Time (WebSockets)

Panduan setup chat real-time supaya mesej muncul tanpa refresh.

---

## Cadangan stack

| Stack | Kebaikan | Keburukan | Cadangan |
|-------|----------|-----------|----------|
| **Laravel Reverb** | Rasmi Laravel, self-hosted, protocol serasi Pusher | Perlu jalankan server Reverb + queue | ✅ **Disyorkan** untuk project ini |
| **Pusher** | Hosted, tiada server untuk maintain | Berbayar untuk trafik tinggi | Alternatif jika tak mahu maintain server |
| **Socket.io** | Popular, banyak contoh | Perlu adapter Laravel / backend lain, bukan “first-class” dalam Laravel | Kurang sesuai untuk Laravel + Echo |

**Kesimpulan:** Gunakan **Laravel Reverb** (sudah ada dalam config). Jika hosting tidak menyokong WebSocket, boleh tukar ke **Pusher** dengan ubah `BROADCAST_CONNECTION=pusher` dan konfigurasi Pusher.

---

## Aliran real-time

1. **Backend:** Selepas mesej disimpan ke DB, event `MessageSent` di-broadcast ke channel private `conversation.{uuid}` dan `user.{recipientId}`.
2. **Frontend:** Laravel Echo subscribe ke `conversation.{uuid}` dan listen `.message.sent`; bila terima event, append mesej ke senarai tanpa refresh.
3. **Had Ahli Lite:** Semua limit (5 mesej per perbualan, 10 peserta, 2 responder) dikawal **di server** dalam `ChatController::sendToRoom()`. Real-time hanya **memaparkan** mesej; ia **tidak** memintas atau mengubah had Ahli Lite.

---

## Konfigurasi (.env)

Untuk **Reverb**, pastikan:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret

# Server Reverb (untuk Laravel menghantar event)
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Client (browser) connect ke Reverb
REVERB_CLIENT_HOST=127.0.0.1
REVERB_CLIENT_PORT=8080
REVERB_CLIENT_SCHEME=http
```

Untuk production (contoh: HTTPS, domain lain), set `REVERB_CLIENT_*` mengikut URL yang browser guna (contoh: `wss://your-domain.com`).

Event `MessageSent` guna **ShouldBroadcastNow** (broadcast serta-merta), jadi **queue worker tidak wajib** untuk chat real-time.

---

## Menjalankan Reverb (wajib untuk real-time)

Reverb mesti berjalan supaya browser boleh sambung WebSocket dan terima event.

```bash
php82 artisan reverb:start
```

Jika port 8080 disekat (rujuk mesej "access a socket forbidden"), guna port 8081:

1. Dalam `.env` set `REVERB_PORT=8081` (supaya Laravel dan client sama-sama guna 8081).
2. Jalankan:
   ```bash
   php82 artisan reverb:start --host=127.0.0.1 --port=8081
   ```

Biarkan terminal Reverb terbuka. Bila akses dari **localhost** / **127.0.0.1**, app auto-guna `ws://127.0.0.1:{REVERB_PORT}`. Bila akses dari domain lain, set `REVERB_CLIENT_*` mengikut cara anda expose Reverb.

---

## Kod ringkas

### Backend: emit selepas simpan mesej

Lokasi: `App\Http\Controllers\ChatController::sendToRoom()`

```php
$message = Message::create([...]);
$conversation->touch();
broadcast(new MessageSent($message));
```

Event `MessageSent` di-broadcast ke:
- `PrivateChannel('conversation.' . $conversationUuid)` — untuk halaman chat room
- `PrivateChannel('user.' . $recipientId)` — untuk notifikasi global (contoh: footer)

### Frontend: listen event

Lokasi: `resources/views/chat/show.blade.php` (dalam `@push('scripts')`)

- Echo subscribe: `Echo.private('conversation.' + conversationKey)`
- Listen: `.listen('.message.sent', (e) => { ... })`
- Payload: `e.message` (id, body, sender_id, created_at, sender_name, conversation_uuid)
- Mesej dari pengguna semasa (fromMe) diabaikan; mesej dari pihak lain di-append ke DOM dan bunyi/notification dipanggil jika perlu.

---

## Auth private channel

Route `/broadcasting/auth` didaftarkan dalam `routes/web.php` (dalam kumpulan `auth`) supaya Echo boleh auth untuk channel `conversation.*` dan `user.*`. Policy channel ada di `routes/channels.php`.

---

## Ahli Lite

- Had (5 mesej/conversation, 10 peserta, 2 responder) **hanya** disemak di backend dalam `sendToRoom()` sebelum `Message::create()` dan `broadcast()`.
- Real-time hanya menerima dan memaparkan event; tiada logic tambah mesej atau bypass limit di frontend.
- Jika pengguna Ahli Lite melebihi had, `sendToRoom` akan return 403 dan frontend tunjuk modal upgrade; broadcast tidak dihantar untuk permintaan yang ditolak.
