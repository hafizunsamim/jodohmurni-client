<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * API endpoint: update lokasi user (dipanggil oleh JS)
     */
    public function updateLocation(Request $request)
    {
        $me = Auth::user();
        if (!$me) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy'  => ['nullable', 'numeric', 'min:0'],
            'source'    => ['nullable', 'string', 'max:50'],
        ]);

        $me->latitude  = (float) $data['latitude'];
        $me->longitude = (float) $data['longitude'];

        // optional: kalau kau ada column accuracy/source/updated_at memang auto
        // $me->location_accuracy = $data['accuracy'] ?? null;
        // $me->location_source   = $data['source'] ?? null;

        $me->save();

        return response()->json([
            'ok' => true,
            'latitude' => $me->latitude,
            'longitude' => $me->longitude,
        ]);
    }

    public function index()
    {
        $me = Auth::user();
        if (!$me) {
            return redirect()->route('login');
        }

        if (!($me->lite_education_seen ?? false)) {
            return redirect()->route('membership.education');
        }

        $me->syncStatusKeahlianFromSubscription();

        $hasActiveSub = \App\Models\Subscription::where('user_id', $me->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->exists();

        // Ambil preference saya
        $myPref = $me->preferences;
        $candidates = collect();

        // Jika tiada preference (sepatutnya tak berlaku selepas onboarding), kembalikan kosong
        if (!$myPref) {
            return view('dashboard', compact('me', 'candidates'));
        }

        // Range umur dari preference
        $myAgeMin = $myPref->age_min ?? 18;
        $myAgeMax = $myPref->age_max ?? 100;

        // ✅ Normalize country (MY/my/My jadi sama)
        $myCountry = strtoupper(trim((string) $me->country));

        // === LANGKAH 1: DAPATKAN SEMUA CALON POTENSI (ikut gender & path) ===
        $query = User::query()
            ->where('id', '!=', $me->id)
            ->whereNotNull('date_of_birth')
            ->whereNotNull('country')
            ->whereRaw('UPPER(country) = ?', [$myCountry]) // ✅ SEKAT IKUT COUNTRY
            ->whereRaw(
                'FLOOR(DATEDIFF(NOW(), date_of_birth) / 365.25) BETWEEN ? AND ?',
                [$myAgeMin, $myAgeMax]
            );

        // Exclude calon yang saya dah swipe sebelum ni
        $query->whereNotIn('id', function ($q) use ($me) {
            $q->select('target_user_id')
              ->from('user_swipes')
              ->where('user_id', $me->id);
        });

        // Filter ikut gender & path
        if ($me->gender === 'male') {
            // Lelaki cari wanita
            $query->where('gender', 'female');

            if ($me->path === 'monogami') {
                $query->whereIn('path', ['wanita_monogami', 'wanita_terbuka']);
            } elseif ($me->path === 'poligami') {
                $query->whereIn('path', ['wanita_poligami', 'wanita_terbuka']);

                if ($me->poligami_situation === 1 || $me->poligami_situation === 2) {
                    $query->whereIn('poligami_level', [1, 2]);
                } elseif ($me->poligami_situation === 3) {
                    $query->where('poligami_level', 3);
                }
            }
        } elseif ($me->gender === 'female') {
            // Wanita cari lelaki
            $query->where('gender', 'male');

            if ($me->path === 'wanita_monogami') {
                $query->where('path', 'monogami');
            } elseif (in_array($me->path, ['wanita_poligami', 'wanita_terbuka'], true)) {
                $query->where('path', 'poligami');

                if ($me->poligami_level === 1) {
                    $query->where('poligami_situation', 1);
                } elseif ($me->poligami_level === 2) {
                    $query->whereIn('poligami_situation', [1, 2]);
                } elseif ($me->poligami_level === 3) {
                    $query->whereIn('poligami_situation', [2, 3]);
                }
            }
        }

        // Dapatkan senarai ID calon sebelum filter lokasi
        $allCandidateIds = $query->pluck('id')->toArray();

        // === LANGKAH 2: FILTER LOKASI (JIKA ADA KOORDINAT & PREFERENCE) ===
        if (
            $me->latitude &&
            $me->longitude &&
            $myPref->location_radius &&
            !empty($allCandidateIds)
        ) {
            // Tentukan max distance
            $maxKm = match ($myPref->location_radius) {
                '50km' => 50,
                '51-150km' => 150,
                '151km_ke_atas' => 99999,
                'tak_kisah' => 99999,
                default => 99999,
            };

            if ($maxKm < 99999) {
                // Kira jarak sebenar & dapatkan ID dalam radius
                $nearbyIds = DB::select("
                    SELECT id
                    FROM users
                    WHERE id IN (" . implode(',', array_fill(0, count($allCandidateIds), '?')) . ")
                      AND latitude IS NOT NULL
                      AND longitude IS NOT NULL
                      AND (
                        6371 * acos(
                            cos(radians(?)) *
                            cos(radians(latitude)) *
                            cos(radians(longitude) - radians(?)) +
                            sin(radians(?)) *
                            sin(radians(latitude))
                        )
                      ) <= ?
                ", array_merge(
                    $allCandidateIds,
                    [$me->latitude, $me->longitude, $me->latitude, $maxKm]
                ));

                $finalIds = array_column($nearbyIds, 'id');
            } else {
                // 'tak_kisah' atau '151km_ke_atas' → ambil semua yang ada lokasi
                $finalIds = User::whereIn('id', $allCandidateIds)
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->pluck('id')
                    ->toArray();
            }

            // Dapatkan calon akhir
            $candidates = User::whereIn('id', $finalIds)
                ->orderByDesc('created_at')
                ->get();
        } else {
            // Tiada lokasi → ambil semua calon
            $candidates = User::whereIn('id', $allCandidateIds)
                ->orderByDesc('created_at')
                ->get();
        }

        return view('dashboard', [
            'me' => $me,
            'candidates' => $candidates,
            'hasActiveSub' => $hasActiveSub,
        ]);
    }
}
