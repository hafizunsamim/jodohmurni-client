<?php

return [
    'mode' => env('PAYPAL_MODE', 'sandbox'),
    'client_id' => env('PAYPAL_CLIENT_ID', ''),
    'secret' => env('PAYPAL_SECRET', ''),
    'currency' => env('PAYPAL_CURRENCY', 'MYR'),
    'cache_order_ttl_minutes' => (int) env('PAYPAL_CACHE_ORDER_TTL', 60),
    // Checkout locale (e.g. en_US). Affects PayPal hosted page language/experience.
    'locale' => env('PAYPAL_LOCALE', 'en_US'),
];
