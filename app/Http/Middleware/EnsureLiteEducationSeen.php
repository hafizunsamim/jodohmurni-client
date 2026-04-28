<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect pengguna ke skrin pendidikan Ahli LITE jika mereka belum acknowledge.
 * Boleh diguna pada route group untuk semua laman yang memerlukan "education seen".
 */
class EnsureLiteEducationSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }
        if (!($user->lite_education_seen ?? false)) {
            return redirect()->route('membership.education');
        }
        return $next($request);
    }
}
