<?php

require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\VAPID;

// Generate VAPID keys
$vapidKeys = VAPID::createVapidKeys();

echo 'Public Key: ' . $vapidKeys['publicKey'] . PHP_EOL;
echo 'Private Key: ' . $vapidKeys['privateKey'] . PHP_EOL;
