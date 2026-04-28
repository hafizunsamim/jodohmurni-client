# Sistem Keahlian (Membership Tiering) — JodohMurni

## 1. Status Keahlian (users.status_keahlian)

| Nilai     | Maksud |
|----------|--------|
| **LITE**   | Default; belum subscribe. Had sapaan/chat dan gambar kabur ikut peraturan LITE. |
| **ACTIVE** | Sudah subscribe; tiada had. |
| **GRADUATE** | Tamat tempoh (pernah subscribe, kini tidak aktif). |
| **HYPE**   | Tamat tempoh kemudian resubscribe. |

- `users.lite_education_seen` (boolean): sama ada pengguna telah melihat skrin pendidikan limitasi LITE dan klik salah satu butang.

## 2. Struktur Database (sapaan & mesej)

Tiada jadual baru. Pengiraan had LITE guna data sedia ada:

- **Jumlah sapaan (10 calon):** `conversations` + `messages`: pengguna LITE dikira “pernah sapa” bila ada sekurang-kurangnya satu `messages` dengan `sender_id` = user tersebut. Urutan ikut `MIN(messages.created_at)` per conversation, ambil 10 conversation pertama.
- **2 calon yang reply:** conversation yang ada sekurang-kurangnya satu mesej dari `sender_id` ≠ user (calon yang membalas). Kira bilangan “calon yang sudah reply”; had 2.
- **5 mesej per calon:** `Message::where('conversation_id', $id)->where('sender_id', $me->id)->count()` ≤ 5.

**Cadangan jika mahu cache/audit:** boleh tambah jadual `user_greeting_stats` (user_id, first_greeted_at, conversation_id) atau audit log, tetapi untuk sekarang pengiraan dari `conversations` + `messages` mencukupi.

## 3. Middleware / Sekatan (Restriction)

- **Skrin pendidikan:** `EnsureLiteEducationSeen` — redirect ke `membership.education` jika `lite_education_seen` false. Sekarang semakan yang sama dilakukan dalam controller (Dashboard, Chat, Profile) supaya tiada laman utama boleh diakses tanpa melihat pendidikan.
- **Had chat (Ahli LITE):** dalam `ChatController`: `freeTrialGateForConversation()`, `freeMessagesSentCountInConversation()`, `freeParticipantsMessagedSet()`, `freeRespondersSet()`. Sekatan hanya dipakai bila `$me->isLite()`.
- **Visibiliti gambar:** dalam `CandidateController`: `canViewClearImage` dikira dari `$viewer->getFirstTenGreetedUserIds()` — hanya 10 calon pertama yang disapa boleh lihat gambar jelas; selain itu blur.

Untuk sekatan berpusat pada “setiap request mesej atau paparan profil”, cadangan:

1. **Chat:** kekal dalam `ChatController::sendToRoom()` dan `showRoom()` — semak `$me->isLite()` dan panggil `freeTrialGateForConversation()` serta had mesej.
2. **Profil/gambar:** kekal dalam `CandidateController::show()` — set `canViewClearImage` ikut status viewer dan senarai 10 sapaan pertama.
3. **Pilihan:** daftarkan `EnsureLiteEducationSeen` pada route group `auth` untuk semua route kecuali `membership.education` dan `membership.acknowledge-*`, supaya satu tempat mengawal “education seen”.

## 4. Aliran (Flow)

1. Daftar → `status_keahlian = LITE`, redirect ke `membership.education`.
2. Skrin pendidikan: dua butang — “Kekal Ahli Lite” (set `lite_education_seen`, ke dashboard) atau “Naik taraf” (set `lite_education_seen`, ke halaman subscription).
3. Semasa subscribe: `activateSubscriptionForUser()` set `status_keahlian` ke ACTIVE (atau HYPE jika sebelum ini GRADUATE).
4. Bila subscription tamat: `User::syncStatusKeahlianFromSubscription()` (dipanggil dari dashboard) set status ke GRADUATE jika tiada langganan aktif.
