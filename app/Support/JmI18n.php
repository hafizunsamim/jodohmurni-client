<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

final class JmI18n
{
    /** @var array<string, array<string, array<string, string>>>|null */
    private static ?array $messages = null;

    /** @return array<string, array<string, array<string, string>>> */
    public static function all(): array
    {
        return self::load();
    }

    public static function country(): string
    {
        $country = (string) session('landing.country', 'MY');
        return in_array($country, ['MY', 'ID', 'SG', 'BN'], true) ? $country : 'MY';
    }

    public static function locale(?string $country = null): string
    {
        $country = $country ?: self::country();
        $locale = (string) session('landing.locale', self::defaultLocaleByCountry($country));
        $allowed = self::allowedLocalesByCountry($country);
        return in_array($locale, $allowed, true) ? $locale : self::defaultLocaleByCountry($country);
    }

    /** @return array<int, string> */
    public static function allowedLocalesByCountry(string $country): array
    {
        return $country === 'ID' ? ['en', 'id'] : ['en', 'ms'];
    }

    public static function defaultLocaleByCountry(string $country): string
    {
        return $country === 'ID' ? 'id' : 'ms';
    }

    /**
     * Translate a key for current session country+locale.
     *
     * @param  array<string, string|int|float>  $replace
     */
    public static function t(string $key, array $replace = [], ?string $fallback = null, ?string $country = null, ?string $locale = null): string
    {
        $country = $country ?: self::country();
        $locale = $locale ?: self::locale($country);

        $messages = self::load();
        $byCountry = $messages[$country] ?? $messages['MY'] ?? [];
        $pack = $byCountry[$locale] ?? $byCountry['en'] ?? [];

        $value = $pack[$key] ?? ($byCountry['en'][$key] ?? null);
        if (!is_string($value) || $value === '') {
            $value = $fallback ?? $key;
        }

        if ($replace) {
            foreach ($replace as $k => $v) {
                $value = str_replace(':' . $k, (string) $v, $value);
            }
        }

        return $value;
    }

    /** @return array<string, array<string, array<string, string>>> */
    private static function load(): array
    {
        if (self::$messages !== null) {
            return self::$messages;
        }

        $path = resource_path('lang/messages.php');
        if (!File::exists($path)) {
            self::$messages = [];
            return self::$messages;
        }

        $data = include $path;
        self::$messages = is_array($data) ? $data : [];

        return self::$messages;
    }
}

