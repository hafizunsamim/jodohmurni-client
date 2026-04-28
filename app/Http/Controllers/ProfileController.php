<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\AffiliateProRequest;
use App\Models\UserAffiliateCode;
use App\Services\AffiliateTierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(AffiliateTierService $tiers)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        if (!($user->lite_education_seen ?? false)) return redirect()->route('membership.education');

        $user->load('preferences');

        $affiliateProfile = $tiers->profileFor($user);

        // ensure affiliate code exists ONLY if eligible (non-LITE)
        $aff = null;
        if ($affiliateProfile['enabled']) {
            $aff = UserAffiliateCode::where('user_id', $user->id)->first();
            if (!$aff) {
                do {
                    $code = strtoupper(Str::random(10));
                    $exists = UserAffiliateCode::where('code', $code)->exists();
                } while ($exists);

                $aff = UserAffiliateCode::create([
                    'user_id' => $user->id,
                    'code' => $code,
                    'clicks' => 0,
                ]);
            }
        }

        $latestProReq = null;
        $proReqHistory = collect();
        if ($affiliateProfile['enabled']) {
            $proReqHistory = AffiliateProRequest::query()
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->limit(10)
                ->get();
            $latestProReq = $proReqHistory->first();
        }

        $activeSub = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        $pkg = null;
        if ($activeSub) {
            $pkg = SubscriptionPackage::find($activeSub->package_id);
        }

        return view('profile.show', [
            'user' => $user,
            'prefs' => $user->preferences,
            'affiliate' => $aff,
            'affiliateProfile' => $affiliateProfile,
            'latestProReq' => $latestProReq,
            'proReqHistory' => $proReqHistory,
            'activeSub' => $activeSub,
            'activePkg' => $pkg,
        ]);
    }


    public function edit()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $user->load('preferences');

        return view('profile.edit', [
            'user' => $user,
            'prefs' => $user->preferences,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'nickname' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],

            'email' => ['required', 'email', 'max:190', 'unique:users,email,' . $user->id . ',id'],

            'country' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],

            'gender' => ['nullable', 'string', 'max:20'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],

            'occupation_type' => ['nullable', 'string', 'max:100'],
            'education_level' => ['nullable', 'string', 'max:100'],

            // comma separated
            'hobbies' => ['nullable', 'string', 'max:2000'],
            'social_activities' => ['nullable', 'string', 'max:2000'],

            // photos
            'photo_1' => ['nullable', 'image', 'max:4096'],
            'photo_2' => ['nullable', 'image', 'max:4096'],
            'photo_3' => ['nullable', 'image', 'max:4096'],
            'photo_4' => ['nullable', 'image', 'max:4096'],

            // optional: remove photo checkbox
            'remove_photo_1' => ['nullable'],
            'remove_photo_2' => ['nullable'],
            'remove_photo_3' => ['nullable'],
            'remove_photo_4' => ['nullable'],

            // preferences (kalau kau ada field2 lain dalam UserPreference, tambah sini)
            'pref_min_age' => ['nullable', 'integer', 'min:18', 'max:99'],
            'pref_max_age' => ['nullable', 'integer', 'min:18', 'max:99'],
        ]);

        // sanitize hobbies/social (kemas comma)
        $data['hobbies'] = $this->normalizeCommaList($data['hobbies'] ?? null);
        $data['social_activities'] = $this->normalizeCommaList($data['social_activities'] ?? null);

        // handle remove photos
        foreach ([1,2,3,4] as $i) {
            $removeKey = "remove_photo_{$i}";
            $photoKey  = "photo_{$i}";

            if ($request->boolean($removeKey)) {
                $old = $user->{$photoKey};
                if ($old) $this->deletePublicFileIfExists($old);
                $user->{$photoKey} = null;
            }
        }

        // handle upload photos (public disk => storage/app/public/...)
        foreach ([1,2,3,4] as $i) {
            $photoKey = "photo_{$i}";
            if ($request->hasFile($photoKey)) {
                $old = $user->{$photoKey};
                if ($old) $this->deletePublicFileIfExists($old);

                $path = $request->file($photoKey)->store('uploads/profile', 'public');
                // simpan format yang compatible dengan asset(): "storage/..."
                $user->{$photoKey} = 'storage/' . $path;
            }
        }

        // update user fields
        $user->fill([
            'name' => $data['name'],
            'nickname' => $data['nickname'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],

            'country' => $data['country'] ?? null,
            'state' => $data['state'] ?? null,
            'district' => $data['district'] ?? null,

            'gender' => $data['gender'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,

            'occupation_type' => $data['occupation_type'] ?? null,
            'education_level' => $data['education_level'] ?? null,

            'hobbies' => $data['hobbies'] ?? null,
            'social_activities' => $data['social_activities'] ?? null,
        ]);

        $user->save();

        // update/create preferences (kalau table preference wujud & relationship ok)
        // NOTE: sesuaikan nama field ikut kolum sebenar dalam user_preferences
        $prefsPayload = [
            'min_age' => $data['pref_min_age'] ?? null,
            'max_age' => $data['pref_max_age'] ?? null,
        ];

        // buang null semua kalau kau tak nak overwrite
        // $prefsPayload = array_filter($prefsPayload, fn($v) => !is_null($v));

        if ($user->preferences) {
            $user->preferences->update($prefsPayload);
        } else {
            $user->preferences()->create($prefsPayload);
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile berjaya dikemaskini.');
    }

    private function normalizeCommaList(?string $s): ?string
    {
        $s = trim((string) $s);
        if ($s === '') return null;

        $parts = array_filter(array_map(function ($x) {
            $x = trim($x);
            // buang double spaces
            $x = preg_replace('/\s+/', ' ', $x);
            return $x;
        }, explode(',', $s)));

        // unique tapi kekalkan order
        $seen = [];
        $out = [];
        foreach ($parts as $p) {
            $k = mb_strtolower($p);
            if (isset($seen[$k])) continue;
            $seen[$k] = true;
            $out[] = $p;
        }

        return $out ? implode(', ', $out) : null;
    }

    private function deletePublicFileIfExists(string $assetPath): void
    {
        // expected: "storage/uploads/profile/xxx.jpg"
        // convert to disk path: "uploads/profile/xxx.jpg"
        if (str_starts_with($assetPath, 'storage/')) {
            $diskPath = substr($assetPath, strlen('storage/'));
            if (Storage::disk('public')->exists($diskPath)) {
                Storage::disk('public')->delete($diskPath);
            }
        }
    }


}