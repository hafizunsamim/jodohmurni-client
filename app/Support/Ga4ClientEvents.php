<?php

namespace App\Support;

use App\Models\Subscription;
use App\Models\SubscriptionPackage;

/**
 * Hantar event GA4 dari pelayan dengan flash sesi — dilaksana sebagai gtag() pada respons HTML berikut.
 */
final class Ga4ClientEvents
{
    public static function purchaseParams(Subscription $sub, SubscriptionPackage $pkg): array
    {
        $amountSen = (int) $sub->amount_sen;
        $value = round($amountSen / 100, 2);

        return [
            'transaction_id' => (string) ($sub->uuid ?? $sub->id),
            'value' => $value,
            'currency' => (string) ($sub->currency ?? 'MYR'),
            'items' => [[
                'item_id' => (string) $pkg->id,
                'item_name' => (string) ($pkg->name ?? 'subscription'),
                'price' => $value,
                'quantity' => 1,
            ]],
        ];
    }

    public static function queue(string $name, array $params = []): void
    {
        if (! config('analytics.google_measurement_id')) {
            return;
        }

        $request = request();
        $events = $request->session()->get('ga4_client_events', []);
        if (! is_array($events)) {
            $events = [];
        }
        $events[] = ['name' => $name, 'params' => $params];
        $request->session()->flash('ga4_client_events', $events);
    }

    public static function queuePurchase(Subscription $sub, SubscriptionPackage $pkg): void
    {
        self::queue('purchase', self::purchaseParams($sub, $pkg));
    }

    /**
     * @return list<array{name: string, params: array<string, mixed>}>
     */
    public static function purchaseEventPayload(Subscription $sub, SubscriptionPackage $pkg): array
    {
        return [
            ['name' => 'purchase', 'params' => self::purchaseParams($sub, $pkg)],
        ];
    }
}
