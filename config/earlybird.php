<?php

return [
    'is_early_bird_active' => env('EARLY_BIRD_ACTIVE', false),
    'force_active'          => env('EARLY_BIRD_FORCE_ACTIVE', null),
    'max_users'             => (int) env('EARLY_BIRD_MAX_USERS', 300),
];
