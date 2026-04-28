<?php

return [
    'sandbox' => env('TOYYIBPAY_SANDBOX', true),
    'user_secret_key' => env('TOYYIBPAY_USER_SECRET_KEY', ''),
    'category_code' => env('TOYYIBPAY_CATEGORY_CODE', ''),
    'cache_bill_ttl_minutes' => (int) env('TOYYIBPAY_CACHE_BILL_TTL', 60),
];
