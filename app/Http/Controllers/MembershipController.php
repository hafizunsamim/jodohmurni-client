<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    /**
     * Halaman maklumat keahlian: perbandingan LITE, ACTIVE, GRADUATE, HYPE.
     * Boleh diakses tanpa login (dari drawer "Keahlian").
     */
    public function info()
    {
        return view('membership.info');
    }

    /**
     * Papar skrin pendidikan limitasi Ahli LITE (selepas daftar).
     * Modal/screen dengan dua butang: kekal LITE atau naik taraf.
     */
    public function education(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('membership.education', [
            'user' => $user,
        ]);
    }

    /**
     * User pilih "Saya faham & kekal Ahli Lite" — set lite_education_seen, redirect dashboard.
     */
    public function acknowledgeLite(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $user->lite_education_seen = true;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Anda kekal sebagai Ahli Lite. Nikmati limitasi percuma.');
    }

    /**
     * User pilih "Saya faham & naik taraf ke Ahli Aktif" — set lite_education_seen, redirect subscription.
     */
    public function acknowledgeUpgrade(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $user->lite_education_seen = true;
        $user->save();

        return redirect()->route('subscription.index')->with('info', 'Pilih pakej untuk naik taraf ke Ahli Aktif.');
    }
}
