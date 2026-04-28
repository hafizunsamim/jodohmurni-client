# Early Bird (Pakej HYPE)

Fungsi Early Bird menawarkan pakej **HYPE** pada harga RM70 (Early Bird) dan mengawal paparan pakej di halaman langganan.

## 1. Database & migration

- **Jadual `settings`**  
  Simpan `is_early_bird_active` (0/1).

- **Pakej HYPE**  
  Satu entri dalam `subscription_packages` dengan:
  - `code` = `HYPE`
  - `name` = `HYPE (Early Bird)`
  - `price_sen` = `7000` (RM70)
  - `duration_days` = 365, `gender` = any, `path` = null

Jalankan migration:

```bash
php82 artisan migrate
```

Ini akan:
1. Cipta jadual `settings` (jika belum wujud)
2. Masukkan key `is_early_bird_active`
3. Masukkan pakej HYPE ke `subscription_packages` (jika jadual wujud dan tiada `code='HYPE'`)

## 2. Logik backend

- **Early Bird “berjalan”**  
  `is_early_bird_active` = true **dan** bilangan pengguna yang telah subscribe pakej HYPE (subscription aktif) **&lt; max** (default 300).

- **Bila Early Bird berjalan**
  - Halaman langganan (**checkout**) hanya tunjuk **pakej HYPE**.
  - Sesiapa yang melanggan pakej HYPE akan dapat **status_keahlian = HYPE**.
  - Bila bilangan subscriber mencapai max (300), Early Bird automatik dimatikan (`is_early_bird_active` = off).

- **Bila Early Bird tidak berjalan** (dimatikan atau cap subscriber dicapai)
  - Pakej HYPE **disembunyikan** dari halaman langganan.
  - Pakej lain dipaparkan seperti biasa.
  - Percubaan langgan pakej HYPE (e.g. melalui form/PayPal) akan ditolak dengan mesej “Pakej Early Bird tidak tersedia pada masa ini.”

## 3. On/Off Early Bird

### Via Artisan (php82)

```bash
# Lihat status
php82 artisan earlybird:status

# Aktifkan
php82 artisan earlybird:toggle --on

# Matikan
php82 artisan earlybird:toggle --off
```

### Via browser (admin)

- URL: **`/admin/earlybird`** (perlu login).
- Di sini anda boleh:
  - Lihat status Early Bird dan bilangan subscriber (X / max).
  - Tukar status (Aktif / Matikan).

## 4. Override melalui .env (pilihan)

Dalam `.env` boleh set:

- `EARLY_BIRD_ACTIVE=true` atau `false`  
- `EARLY_BIRD_MAX_USERS=300` – bilangan maksimum pengguna early bird (default 300)
- `EARLY_BIRD_FORCE_ACTIVE=true` – paksa Early Bird aktif (untuk ujian)

Nilai dalam jadual `settings` (dan admin/command) akan override .env melainkan `EARLY_BIRD_FORCE_ACTIVE` diset.

## 5. Validasi ringkas

- Semasa **is_early_bird_active = true** dan bilangan subscriber HYPE &lt; max, pengguna yang langgan hanya ditawarkan pakej HYPE dan akan dapat status **HYPE** selepas pembayaran.
- Bila bilangan subscriber mencapai max (atau Early Bird dimatikan manual), sistem tidak lagi menawarkan pakej HYPE di checkout dan menolak percubaan langgan pakej HYPE.
