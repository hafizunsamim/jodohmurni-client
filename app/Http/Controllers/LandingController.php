<?php

namespace App\Http\Controllers;

use App\Models\LocationCountry;
use App\Models\LocationRegion;
use App\Models\LocationDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Models\UserAffiliateCode;
class LandingController extends Controller
{
    // ✅ mapping video ikut country code (ubah ikut code negara dalam DB kau)
    private array $videoByCountry = [
        'MY' => 'assets/videos/JodohMurniMV_MY.mp4',
        'ID' => 'assets/videos/JodohMurniMV_ID.mp4',
        'SG' => 'assets/videos/JodohMurniMV_SG.mp4',
        'BN' => 'assets/videos/JodohMurniMV_BN.mp4',
    ];

    // ✅ mapping locale ikut country
    private array $localeByCountry = [
        'MY' => 'ms',
        'BN' => 'ms',
        'SG' => 'ms',
        'ID' => 'id',
    ];

    public function index(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // =========================
        // Guest sahaja lalu bawah ni
        // =========================
        $gender = $request->query('gender');
        if (!in_array($gender, ['lelaki', 'perempuan'])) {
            $gender = null;
        }

        // ✅ session country/locale
        $selectedCountry = $request->session()->get('landing.country'); // contoh: MY/ID/SG/BN
        $locale = $request->session()->get('landing.locale', 'ms');

        // set locale untuk view (kalau kau guna __() translation)
        App::setLocale($locale);

        $countries = LocationCountry::orderBy('name')->get();

        // (optional) kau boleh kekalkan locationData kalau kau perlukan
        $locationData = [];
        foreach ($countries as $country) {
            $regions = LocationRegion::where('country_code', $country->code)
                ->orderBy('name')
                ->get();

            $data = [];
            foreach ($regions as $region) {
                $districts = LocationDistrict::where('region_id', $region->id)
                    ->orderBy('name')
                    ->pluck('name')
                    ->toArray();

                $data[$region->name] = $districts;
            }

            $locationData[$country->code] = [
                'levels' => ['Negeri/Wilayah', 'Daerah'],
                'data'   => $data,
            ];
        }

        // ✅ pilih video ikut country (fallback ke video default)
        $videoSrc = 'assets/videos/JodohMurniMV.mp4';
        if ($selectedCountry && isset($this->videoByCountry[$selectedCountry])) {
            $videoSrc = $this->videoByCountry[$selectedCountry];
        }


    $ref = $request->query('ref');
    if ($ref && is_string($ref)) {
        $ref = trim($ref);

        if ($ref !== '') {
            // simpan code untuk digunakan masa subscribe
            $request->session()->put('affiliate.code', $ref);

            // ✅ unique-per-session click
            $clickKey = "affiliate.clicked." . $ref;
            if (!$request->session()->has($clickKey)) {
                $row = UserAffiliateCode::where('code', $ref)->first();
                if ($row) {
                    // column name: clicks
                    $row->increment('clicks');
                }
                $request->session()->put($clickKey, 1);
            }
        }
    }


        return view('landing', compact(
            'countries',
            'locationData',
            'gender',
            'selectedCountry',
            'locale',
            'videoSrc'
        ));
    }

    // ✅ dipanggil bila user tekan negara dalam modal landing
    public function setCountry(Request $request)
    {
        $request->validate([
            'country' => ['required', 'exists:location_countries,code'],
        ]);

        $code = $request->input('country');

        $locale = $this->localeByCountry[$code] ?? 'ms';

        $request->session()->put('landing.country', $code);
        $request->session()->put('landing.locale', $locale);

        // ✅ kalau kau nak onboarding terus guna country ni
        $onb = $request->session()->get('onboarding', []);
        $onb['country'] = $code;
        unset($onb['state'], $onb['district'], $onb['region_id'], $onb['district_id']);
        $request->session()->put('onboarding', $onb);

        // ✅ trigger modal #2 lepas reload
        $request->session()->flash('show_first_time_notice', true);

        return response()->json(['ok' => true]);
    }
}
