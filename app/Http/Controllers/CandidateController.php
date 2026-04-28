<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CandidateController extends Controller
{
    public function show(User $user)
    {
        // ✅ kalau buka profile sendiri, redirect
        if (Auth::check() && $user->id === Auth::id()) {
            return redirect()->route('profile.show');
        }

        $viewer = Auth::user();

        // ✅ Check subscription aktif (sama macam dashboard)
        $hasActiveSub = false;
        if ($viewer) {
            $hasActiveSub = \App\Models\Subscription::where('user_id', $viewer->id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('ends_at')
                      ->orWhere('ends_at', '>=', now());
                })
                ->exists();
        }

        $user->loadMissing('preferences');

        // ✅ Display name (HIDE kalau tak subscribe)
        if ($hasActiveSub) {
            // ikut dashboard: nickname + name
            $displayName = trim((string) (($user->nickname ?? '') . ' ' . ($user->name ?? '')));
            if ($displayName === '') $displayName = trim((string) ($user->name ?? ''));
            if ($displayName === '') $displayName = trim((string) ($user->nickname ?? ''));
            if ($displayName === '') $displayName = Str::before((string) $user->email, '@');
        } else {
            $displayName = trim((string) ($user->public_id ?? ''));
            if ($displayName === '') $displayName = 'Calon';
        }

        // ✅ Photos
        $photos = array_values(array_filter([
            $user->photo_1,
            $user->photo_2,
            $user->photo_3,
            $user->photo_4,
        ]));

        $mainPhoto = !empty($photos) ? asset($photos[0]) : asset('images/default.jpg');

        // ✅ Age
        $age = $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->age : null;

        // ✅ Interests
        $hobbies = $user->hobbies ? array_map('trim', explode(',', $user->hobbies)) : [];
        $social  = $user->social_activities ? array_map('trim', explode(',', $user->social_activities)) : [];
        $interests = array_values(array_filter(array_unique(array_merge($hobbies, $social))));

        // ✅ Distance
        $distanceKm = null;
        if ($viewer && $viewer->latitude && $viewer->longitude && $user->latitude && $user->longitude) {
            $distanceKm = $this->haversineKm(
                (float) $viewer->latitude,
                (float) $viewer->longitude,
                (float) $user->latitude,
                (float) $user->longitude
            );
        }

        // ✅ Visibiliti gambar (Ahli LITE): jelas hanya untuk 10 calon pertama yang disapa; ACTIVE sentiasa jelas
        $canViewClearImage = true;
        if ($viewer && !$hasActiveSub) {
            $firstTenGreetedIds = $viewer->getFirstTenGreetedUserIds();
            $canViewClearImage = in_array($user->id, $firstTenGreetedIds, true);
        }

        return view('candidates.show', compact(
            'user',
            'displayName',
            'photos',
            'mainPhoto',
            'age',
            'interests',
            'distanceKm',
            'hasActiveSub',
            'canViewClearImage'
        ));
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            (sin($dLon / 2) ** 2);

        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
