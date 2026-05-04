<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 (gtag.js)
    |--------------------------------------------------------------------------
    |
    | Set GOOGLE_ANALYTICS_MEASUREMENT_ID dalam .env (contoh: G-XXXXXXXXXX).
    | Kosongkan untuk matikan tag di persekitaran dev/staging.
    |
    | Ringkasan event tersuai JodohMurni:
    | - jm_homepage_visit — lawatan tetamu ke /
    | - sign_up — pendaftaran lengkap
    | - login — log masuk berjaya
    | - subscription_package_click — klik mula langganan / pilih pakej
    | - purchase — langganan diaktifkan (GA4 standard; transaction_id = UUID subscription)
    | - view_profile — lihat profil (sendiri atau calon)
    | - like_profile — swipe / suka calon
    | - match_success — suka timbal balik (mutual like)
    |
    */

    'google_measurement_id' => env('GOOGLE_ANALYTICS_MEASUREMENT_ID', ''),

];
