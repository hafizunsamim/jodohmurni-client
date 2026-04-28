<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalAffiliateAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = mb_strtolower(trim((string) $credentials['email']));
        $password = (string) $credentials['password'];

        if (Auth::attempt(['email' => $email, 'password' => $password], false)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user && !empty($user->is_external_affiliate)) {
                return redirect()->route('affiliate.external.dashboard');
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akaun ini bukan akaun Affiliate Pro (external).',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Emel atau kata laluan tidak sah.',
        ])->onlyInput('email');
    }
}

