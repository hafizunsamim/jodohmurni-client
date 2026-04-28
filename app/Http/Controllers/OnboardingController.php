<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\MasterHobby;
use App\Models\MasterSocialActivity;
use App\Models\LocationCountry;
use App\Models\LocationRegion;
use App\Models\LocationDistrict;

class OnboardingController extends Controller
{
    protected function getOnboarding(Request $request): array
    {
        return $request->session()->get('onboarding', []);
    }

    protected function putOnboarding(Request $request, array $data): void
    {
        $request->session()->put('onboarding', $data);
    }

    /* ================= STEP ASAL: negara, jantina, dll ================= */

    public function country(Request $request)
    {
        $onb = $this->getOnboarding($request);

        // ✅ kalau landing dah set, skip step country
        if (!empty($onb['country'])) {
            return redirect()->route('onboarding.state');
        }

        // kalau belum ada, balik landing supaya modal country keluar
        return redirect()->route('landing');
    }

    public function storeCountry(Request $request)
    {
        $request->validate([
            'country' => ['required', 'exists:location_countries,code'],
        ]);

        $onb = $this->getOnboarding($request);
        $onb['country'] = $request->country;
        unset($onb['state'], $onb['district'], $onb['region_id'], $onb['district_id']);

        $this->putOnboarding($request, $onb);

        return redirect()->route('onboarding.state');
    }

    public function state(Request $request)
    {
        $onb = $this->getOnboarding($request);

        $countryCode = $onb['country'] ?? null;
        if (!$countryCode) {
            return redirect()->route('onboarding.country');
        }

        $regions = LocationRegion::where('country_code', $countryCode)
            ->orderBy('name')
            ->get();

        return view('onboarding.state', compact('onb', 'regions'));
    }

    public function storeState(Request $request)
    {
        $onb = $this->getOnboarding($request);
        $countryCode = $onb['country'] ?? null;

        if (!$countryCode) {
            return redirect()->route('onboarding.country');
        }

        $request->validate([
            'region_id' => [
                'required',
                Rule::exists('location_regions', 'id')
                    ->where(fn ($q) => $q->where('country_code', $countryCode)),
            ],
        ]);

        $region = LocationRegion::findOrFail($request->region_id);

        $onb['region_id'] = $region->id;
        $onb['state'] = $region->name;

        unset($onb['district'], $onb['district_id']);

        $this->putOnboarding($request, $onb);

        return redirect()->route('onboarding.district');
    }

    public function district(Request $request)
    {
        $onb = $this->getOnboarding($request);
        $country = $onb['country'] ?? null;
        $regionId = $onb['region_id'] ?? null;

        if (!$country) return redirect()->route('onboarding.country');
        if (!$regionId) return redirect()->route('onboarding.state');

        $districts = LocationDistrict::where('region_id', $regionId)->orderBy('name')->get();
        return view('onboarding.district', compact('onb', 'districts'));
    }

    public function storeDistrict(Request $request)
    {
        $onb = $this->getOnboarding($request);
        $regionId = $onb['region_id'] ?? null;
        if (!$regionId) return redirect()->route('onboarding.state');

        $request->validate([
            'district_id' => [
                'required',
                Rule::exists('location_districts', 'id')->where(fn ($q) => $q->where('region_id', $regionId))
            ],
        ]);

        $district = LocationDistrict::findOrFail($request->district_id);
        $onb['district_id'] = $district->id;
        $onb['district'] = $district->name;

        $this->putOnboarding($request, $onb);

        return redirect()->route('onboarding.gender');
    }

    public function gender(Request $request)
    {
        $onb = $this->getOnboarding($request);
        return view('onboarding.gender', compact('onb'));
    }

    public function storeGender(Request $request)
    {
        $request->validate(['gender' => 'required|in:male,female']);

        $onb = $this->getOnboarding($request);
        $onb['gender'] = $request->gender;

        unset($onb['marital_status'], $onb['path'], $onb['poligami_situation'], $onb['poligami_level']);
        $this->putOnboarding($request, $onb);

        if ($request->gender === 'male') {
            return redirect()->route('onboarding.male.status');
        }
        return redirect()->route('onboarding.female.preference');
    }

    /* ================= LELAKI ================= */

    public function maleStatus(Request $request)
    {
        $onb = $this->getOnboarding($request);
        if (($onb['gender'] ?? null) !== 'male') {
            return redirect()->route('landing');
        }
        return view('onboarding.male-status', compact('onb'));
    }

    public function storeMaleStatus(Request $request)
    {
        $request->validate([
            'marital_status' => 'required|in:single,divorced,widowed,ex_polygamous,married_1,married_2,married_3'
        ]);

        $onb = $this->getOnboarding($request);
        $onb['marital_status'] = $request->marital_status;
        unset($onb['path'], $onb['poligami_situation']);

        if (in_array($request->marital_status, ['single', 'divorced', 'widowed', 'ex_polygamous'], true)) {
            $onb['path'] = 'monogami';
        } else {
            $onb['path'] = 'poligami';
        }

        $this->putOnboarding($request, $onb);

        if ($onb['path'] === 'monogami') {
            return redirect()->route('onboarding.photos');
        }
        return redirect()->route('onboarding.male.polygamy_situation');
    }

    public function maleMonogamyInfo(Request $request)
    {
        $onb = $this->getOnboarding($request);
        if (($onb['gender'] ?? null) !== 'male' || ($onb['path'] ?? null) !== 'monogami') {
            return redirect()->route('onboarding.male.status');
        }
        return view('onboarding.male-monogamy-info', compact('onb'));
    }

    public function malePolygamyConfirm(Request $request)
    {
        $onb = $this->getOnboarding($request);
        if (($onb['gender'] ?? null) !== 'male' || ($onb['path'] ?? null) !== 'poligami') {
            return redirect()->route('onboarding.male.status');
        }
        return view('onboarding.male-polygamy-confirm', compact('onb'));
    }

    public function storeMalePolygamyConfirm(Request $request)
    {
        $request->validate(['confirm' => 'required|in:yes,no']);

        $onb = $this->getOnboarding($request);
        if ($request->confirm === 'no') {
            unset($onb['path'], $onb['marital_status'], $onb['poligami_situation']);
            $this->putOnboarding($request, $onb);
            return redirect()->route('onboarding.male.status');
        }

        $this->putOnboarding($request, $onb);
        return redirect()->route('onboarding.male.polygamy_situation');
    }

    public function malePolygamySituation(Request $request)
    {
        $onb = $this->getOnboarding($request);
        if (($onb['gender'] ?? null) !== 'male' || ($onb['path'] ?? null) !== 'poligami') {
            return redirect()->route('onboarding.male.status');
        }
        return view('onboarding.male-polygamy-situation', compact('onb'));
    }

    public function storeMalePolygamySituation(Request $request)
    {
        $request->validate(['poligami_situation' => 'required|in:1,2,3']);

        $onb = $this->getOnboarding($request);
        if (($onb['gender'] ?? null) !== 'male' || ($onb['path'] ?? null) !== 'poligami') {
            return redirect()->route('onboarding.male.status');
        }

        $onb['poligami_situation'] = (int) $request->poligami_situation;
        $this->putOnboarding($request, $onb);

        return redirect()->route('onboarding.photos');
    }

    public function malePolygamySummary(Request $request)
    {
        $onb = $this->getOnboarding($request);

        if (($onb['gender'] ?? null) !== 'male' || ($onb['path'] ?? null) !== 'poligami' || !isset($onb['poligami_situation'])) {
            return redirect()->route('onboarding.male.status');
        }

        $situation = (int) $onb['poligami_situation'];

        $rawCounts = \App\Models\User::where('gender', 'female')
            ->where('path', 'wanita_poligami')
            ->selectRaw('poligami_level, COUNT(*) as total')
            ->groupBy('poligami_level')
            ->pluck('total', 'poligami_level')
            ->toArray();

        $stats = [1 => $rawCounts[1] ?? 0, 2 => $rawCounts[2] ?? 0, 3 => $rawCounts[3] ?? 0];

        return view('onboarding.male-polygamy-summary', compact('onb', 'situation', 'stats'));
    }

    /* ================= PEREMPUAN ================= */

    public function femalePreference(Request $request)
    {
        $onb = $this->getOnboarding($request);
        if (($onb['gender'] ?? null) !== 'female') {
            return redirect()->route('landing');
        }
        return view('onboarding.female-preference', compact('onb'));
    }

    public function femaleMonogamyInfo(Request $request)
    {
        $onb = $this->getOnboarding($request);
        if (($onb['gender'] ?? null) !== 'female' || ($onb['path'] ?? null) !== 'wanita_monogami') {
            return redirect()->route('onboarding.female.preference');
        }
        return view('onboarding.female-monogamy-info', compact('onb'));
    }

    public function storeFemalePreference(Request $request)
    {
        $request->validate(['target' => 'required|in:monogami,terbuka,poligami']);

        $onb = $this->getOnboarding($request);

        if ($request->target === 'monogami') {
            $onb['path'] = 'wanita_monogami';
            unset($onb['poligami_level']);
            $this->putOnboarding($request, $onb);
            return redirect()->route('onboarding.photos');
        }

        $onb['path'] = $request->target === 'terbuka' ? 'wanita_terbuka' : 'wanita_poligami';
        $this->putOnboarding($request, $onb);

        return redirect()->route('onboarding.female.polygamy_level');
    }

    public function femalePolygamyLevel(Request $request)
    {
        $onb = $this->getOnboarding($request);
        if (($onb['gender'] ?? null) !== 'female' || !in_array(($onb['path'] ?? null), ['wanita_poligami', 'wanita_terbuka'], true)) {
            return redirect()->route('onboarding.female.preference');
        }
        return view('onboarding.female-polygamy-level', compact('onb'));
    }

    public function storeFemalePolygamyLevel(Request $request)
    {
        $request->validate(['poligami_level' => 'required|in:1,2,3']);

        $onb = $this->getOnboarding($request);
        if (($onb['gender'] ?? null) !== 'female' || !in_array(($onb['path'] ?? null), ['wanita_poligami', 'wanita_terbuka'], true)) {
            return redirect()->route('onboarding.female.preference');
        }

        $onb['poligami_level'] = (int) $request->poligami_level;
        $this->putOnboarding($request, $onb);

        return redirect()->route('onboarding.photos');
    }

    public function femalePolygamySummary(Request $request)
    {
        $onb = $this->getOnboarding($request);

        if (($onb['gender'] ?? null) !== 'female' || !in_array(($onb['path'] ?? null), ['wanita_poligami', 'wanita_terbuka'], true) || !isset($onb['poligami_level'])) {
            return redirect()->route('onboarding.female.preference');
        }

        $level = (int) $onb['poligami_level'];

        $rawCounts = \App\Models\User::where('gender', 'male')
            ->where('path', 'poligami')
            ->selectRaw('poligami_situation, COUNT(*) as total')
            ->groupBy('poligami_situation')
            ->pluck('total', 'poligami_situation')
            ->toArray();

        $stats = [1 => $rawCounts[1] ?? 0, 2 => $rawCounts[2] ?? 0, 3 => $rawCounts[3] ?? 0];

        return view('onboarding.female-polygamy-summary', compact('onb', 'level', 'stats'));
    }

    /* ================= UPLOAD GAMBAR (SELFIE + RULES) ================= */

    private function ensureMobileOrAbort(Request $request): void
    {
        $ua = (string) $request->userAgent();
        $isMobile = preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $ua);

        if (!$isMobile) {
            abort(403, 'Onboarding gambar hanya boleh dibuat melalui telefon.');
        }
    }

    private function storeBase64ImageTo(string $dataUrl, string $destPathNoExt): string
    {
        if (!str_starts_with($dataUrl, 'data:image/')) {
            abort(422, 'Selfie format tidak sah.');
        }

        $parts = explode(',', $dataUrl, 2);
        if (count($parts) !== 2) {
            abort(422, 'Selfie format tidak sah.');
        }

        [$meta, $b64] = $parts;

        $ext = 'jpg';
        if (str_contains($meta, 'image/png'))  $ext = 'png';
        if (str_contains($meta, 'image/webp')) $ext = 'webp';
        if (str_contains($meta, 'image/jpeg')) $ext = 'jpg';

        $bin = base64_decode($b64, true);
        if ($bin === false) {
            abort(422, 'Selfie gagal diproses.');
        }

        if (strlen($bin) > 10 * 1024 * 1024) {
            abort(422, 'Selfie terlalu besar.');
        }

        $fullPath = $destPathNoExt . '.' . $ext;
        file_put_contents($fullPath, $bin);

        return $ext;
    }

    /**
     * Simpan base64 image ke Storage disk 'public' (storage/app/public).
     * Sesuai untuk production; tidak bergantung pada permission public/images.
     *
     * @param string $dataUrl data URL (data:image/...;base64,...)
     * @param string $storagePathNoExt path dalam disk tanpa sambungan, e.g. "images/uuid/photo1"
     * @return string sambungan fail, e.g. "jpg"
     */
    private function storeBase64ImageToStorage(string $dataUrl, string $storagePathNoExt): string
    {
        if (!str_starts_with($dataUrl, 'data:image/')) {
            abort(422, 'Selfie format tidak sah.');
        }

        $parts = explode(',', $dataUrl, 2);
        if (count($parts) !== 2) {
            abort(422, 'Selfie format tidak sah.');
        }

        [$meta, $b64] = $parts;

        $ext = 'jpg';
        if (str_contains($meta, 'image/png'))  $ext = 'png';
        if (str_contains($meta, 'image/webp')) $ext = 'webp';
        if (str_contains($meta, 'image/jpeg')) $ext = 'jpg';

        $bin = base64_decode($b64, true);
        if ($bin === false) {
            abort(422, 'Selfie gagal diproses.');
        }

        if (strlen($bin) > 10 * 1024 * 1024) {
            abort(422, 'Selfie terlalu besar.');
        }

        $path = $storagePathNoExt . '.' . $ext;
        Storage::disk('public')->put($path, $bin);

        return $ext;
    }

    public function photos(Request $request)
    {
        $this->ensureMobileOrAbort($request);

        $onb = $this->getOnboarding($request);
        if (!isset($onb['gender'])) {
            return redirect()->route('landing');
        }

        return view('onboarding.photos', compact('onb'));
    }

    public function storePhotos(Request $request)
    {
        $this->ensureMobileOrAbort($request);

        $request->validate([
            'photo_1_data' => 'required|string',
            'photo_2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200',
            'photo_3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200',
            'photo_4' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200',
        ], [
            'photo_1_data.required' => 'Gambar 1 wajib selfie (kamera depan).',
        ]);

        $onb = $this->getOnboarding($request);
        if (empty($onb['photo_uuid'])) {
            $onb['photo_uuid'] = (string) Str::uuid();
        }

        $uuid = $onb['photo_uuid'];
        // Simpan ke storage/app/public (boleh tulis di production); papar melalui asset('storage/...')
        $storageDir = 'images/' . $uuid;

        $saved = [];

        // PHOTO 1 (base64 selfie)
        $selfieData = $request->input('photo_1_data');
        $ext = $this->storeBase64ImageToStorage($selfieData, $storageDir . '/photo1');
        $saved['photo_1'] = 'storage/' . $storageDir . '/photo1.' . $ext;

        // PHOTO 2-4 (file upload)
        foreach ([2, 3, 4] as $i) {
            $key = 'photo_' . $i;
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $path = $file->storeAs($storageDir, 'photo' . $i . '.' . ($file->getClientOriginalExtension() ?: 'jpg'), 'public');
                $saved[$key] = 'storage/' . $path;
            }
        }

        foreach ($saved as $k => $v) {
            $onb[$k] = $v;
        }

        unset($onb['photo_3_hobby_tag']);

        $this->putOnboarding($request, $onb);

        return redirect()->route('onboarding.personal_info');
    }

    /* ================= MAKLUMAT DIRI ================= */

    public function personalInfo(Request $request)
    {
        $onb = $this->getOnboarding($request);
        if (!isset($onb['gender'])) {
            return redirect()->route('landing');
        }

        return view('onboarding.personal-info', compact('onb'));
    }

    public function storePersonalInfo(Request $request)
    {
        $request->validate([
            // ✅ occupation optional (nullable) + enum baru
            'occupation_type'        => 'nullable|in:kerajaan,swasta,berniaga_usahawan_sendiri',

            'date_of_birth'          => 'required|date_format:d-m-Y|before:today|after:1920-01-01',
            'education_level'        => 'required|in:spm,diploma,ijazah_sarjana_muda,ijazah_sarjana_master,doktor_falsafah',

            'hobbies_text'           => 'nullable|string|max:1000',
            'social_activities_text' => 'nullable|string|max:1000',
        ]);

        $onb = $this->getOnboarding($request);

        $onb['date_of_birth']   = $request->date_of_birth;
        $onb['education_level'] = $request->education_level;

        // ✅ store occupation as null if not selected
        $onb['occupation_type'] = $request->filled('occupation_type')
            ? $request->occupation_type
            : null;

        // hobbies
        $hobbiesText = $request->input('hobbies_text', '');
        if ($hobbiesText !== '') {
            $hobbiesArray = array_filter(array_map('trim', explode(',', $hobbiesText)));
            $onb['hobbies'] = $hobbiesArray ? implode(',', $hobbiesArray) : null;
        } else {
            $onb['hobbies'] = null;
        }

        // social activities
        $socialText = $request->input('social_activities_text', '');
        if ($socialText !== '') {
            $socialArray = array_filter(array_map('trim', explode(',', $socialText)));
            $onb['social_activities'] = $socialArray ? implode(',', $socialArray) : null;
        } else {
            $onb['social_activities'] = null;
        }

        // lokasi
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $onb['latitude']  = (float) $request->latitude;
            $onb['longitude'] = (float) $request->longitude;
        }

        $this->putOnboarding($request, $onb);

        return redirect()->route('onboarding.matching_preferences');
    }

    /* ================= CIRI-CIRI CALON IDAMAN ================= */

    public function matchingPreferences(Request $request)
    {
        $onb = $this->getOnboarding($request);
        if (!isset($onb['date_of_birth'])) {
            return redirect()->route('onboarding.personal_info');
        }
        return view('onboarding.matching-preferences', compact('onb'));
    }

    public function storeMatchingPreferences(Request $request)
    {
        $request->validate([
            'age_min' => 'required|integer|min:18|max:100',
            'age_max' => 'required|integer|min:18|max:100|gte:age_min',
            'location_radius' => 'required|in:50km,51-150km,151km_ke_atas,tak_kisah',
        ]);

        $onb = $this->getOnboarding($request);
        $onb['age_min'] = (int) $request->age_min;
        $onb['age_max'] = (int) $request->age_max;
        $onb['location_radius'] = $request->location_radius;

        // willing_to_relocate hanya diset jika dihantar (tiada input untuk wanita di matching-preferences)
        if ($request->filled('willing_to_relocate')) {
            $request->validate(['willing_to_relocate' => 'in:ya,tidak,boleh_dipertimbangkan']);
            $onb['willing_to_relocate'] = $request->willing_to_relocate;
        }

        $this->putOnboarding($request, $onb);

        return redirect()->route('register');
    }
}
