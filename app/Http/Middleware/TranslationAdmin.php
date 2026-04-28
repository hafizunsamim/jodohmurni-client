<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TranslationAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $token = (string) $request->header('X-Admin-Token', '');
        $expected = (string) env('TRANSLATION_ADMIN_TOKEN', '');

        // optional IP allowlist
        $allowIps = trim((string) env('TRANSLATION_ADMIN_ALLOW_IPS', ''));
        if ($allowIps !== '') {
            $allowed = array_filter(array_map('trim', explode(',', $allowIps)));
            $ip = (string) $request->ip();
            if (!in_array($ip, $allowed, true)) {
                return response()->json(['message' => 'Forbidden (IP)'], 403);
            }
        }

        if ($expected === '' || $token === '' || !hash_equals($expected, $token)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
