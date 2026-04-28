<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bypass payment gateway (development / QA sahaja)
    |--------------------------------------------------------------------------
    |
    | Jika true, klik "Bayar" (PayPal / Toyyibpay) akan terus aktifkan subscription
    | tanpa panggil gateway. Jangan aktifkan di production.
    |
    */

    'payment_bypass' => (bool) env('SUBSCRIPTION_PAYMENT_BYPASS', false),

];
