<?php

namespace App\Http\Controllers;

use App\Models\AffiliateReferral;
use App\Models\UserAffiliateCode;
use App\Models\User;
use App\Support\Ga4ClientEvents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegister(Request $request)
    {
        $onboarding = $request->session()->get('onboarding', null);
        return view('auth.register', compact('onboarding'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'nickname'       => 'nullable|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'phone_country'  => 'required|in:MY,ID,SG,BN',
            'phone'          => 'required|string',
            'password'       => 'required|string|min:6|confirmed',
        ]);

        // affiliate code (jika user datang dari link ?ref=CODE dan masih dalam session)
        $affiliateCode = $request->session()->get('affiliate.code');

        // === 1. Generate nickname jika kosong ===
        if (empty(trim($data['nickname'] ?? ''))) {
            $data['nickname'] = $this->generateUniqueNickname();
        }

        // === 2. Validate & format phone number ===
        $countryCode = $data['phone_country'];
        $phoneRaw = preg_replace('/[^0-9]/', '', $data['phone']); // remove non-digits

        $phoneFormatted = null;
        switch ($countryCode) {
            case 'MY': // Malaysia: 9–10 digits after 60 (e.g., 123456789 or 1123456789)
                if (!preg_match('/^(1[0-9]{8,9})$/', $phoneRaw)) {
                    return back()->withErrors([
                        'phone' => 'Nombor telefon Malaysia tidak sah. Contoh: 123456789 (tanpa 0 di hadapan).'
                    ])->withInput();
                }
                $phoneFormatted = '+60' . $phoneRaw;
                break;

            case 'ID': // Indonesia: starts with 8, total 10–12 digits
                if (!preg_match('/^(8[0-9]{9,11})$/', $phoneRaw)) {
                    return back()->withErrors([
                        'phone' => 'Nombor telefon Indonesia tidak sah. Mesti bermula dengan 8 dan 10–12 digit.'
                    ])->withInput();
                }
                $phoneFormatted = '+62' . $phoneRaw;
                break;

            case 'SG': // Singapore: 8 digits, starts with 6, 8, or 9
                if (!preg_match('/^[689][0-9]{7}$/', $phoneRaw)) {
                    return back()->withErrors([
                        'phone' => 'Nombor telefon Singapura tidak sah. Mesti 8 digit dan bermula dengan 6, 8 atau 9.'
                    ])->withInput();
                }
                $phoneFormatted = '+65' . $phoneRaw;
                break;

            case 'BN': // Brunei: 7 digits, starts with 2–8
                if (!preg_match('/^[2-8][0-9]{6}$/', $phoneRaw)) {
                    return back()->withErrors([
                        'phone' => 'Nombor telefon Brunei tidak sah. Mesti 7 digit dan bermula dengan 2 hingga 8.'
                    ])->withInput();
                }
                $phoneFormatted = '+673' . $phoneRaw;
                break;

            default:
                return back()->withErrors(['phone' => 'Negara tidak disokong.'])->withInput();
        }

        // === 3. Ambil data onboarding ===
        $onb = $request->session()->get('onboarding', []);

        // === 4. Create user ===
        $user = User::create([
            'name'               => $data['name'],
            'nickname'           => $data['nickname'],
            'email'              => $data['email'],
            'phone'              => $phoneFormatted, // E.164 format
            'password'           => Hash::make($data['password']),
            'status_keahlian'    => User::STATUS_KEAHLIAN_LITE,
            'country'            => $onb['country']            ?? null,
            'state'              => $onb['state']              ?? null,
            'district'           => $onb['district']           ?? null,
            'gender'             => $onb['gender']             ?? null,
            'marital_status'     => $onb['marital_status']     ?? null,
            'path'               => $onb['path']               ?? null,
            'poligami_situation' => $onb['poligami_situation'] ?? null,
            'poligami_level'     => $onb['poligami_level']     ?? null,
            'photo_1'            => $onb['photo_1']            ?? null,
            'photo_2'            => $onb['photo_2']            ?? null,
            'photo_3'            => $onb['photo_3']            ?? null,
            'photo_3_hobby_tag'  => $onb['photo_3_hobby_tag']  ?? null,
            'photo_4'            => $onb['photo_4']            ?? null,
            'education_level' => $onb['education_level'] ?? null,
            'date_of_birth' => $onb['date_of_birth'] ?? null,
            'hobbies' => $onb['hobbies'] ?? null,
            'social_activities' => $onb['social_activities'] ?? null,
            'occupation_type' => $onb['occupation_type'] ?? null,
            'government_level' => $onb['government_level'] ?? null,
            'government_uniform' => $onb['government_uniform'] ?? null,
            'government_role_level' => $onb['government_role_level'] ?? null,
            'private_role' => $onb['private_role'] ?? null,
            'business_scale' => $onb['business_scale'] ?? null,
            'latitude' => $onb['latitude'] ?? null,
            'longitude' => $onb['longitude'] ?? null,
        ]);

        // === 4b. Simpan referral (wajib: user create akaun guna link affiliate) ===
        if (is_string($affiliateCode)) {
            $affiliateCode = trim($affiliateCode);
        } else {
            $affiliateCode = null;
        }

        if ($affiliateCode) {
            $affRow = UserAffiliateCode::query()
                ->where('code', $affiliateCode)
                ->first();

            // simpan sekali sahaja (1 referred_user_id => 1 referrer_user_id)
            // elak kes pelik: self-referral (tak patut berlaku, tapi kita guard)
            if ($affRow && (string) $affRow->user_id !== (string) $user->id) {
                AffiliateReferral::firstOrCreate(
                    ['referred_user_id' => $user->id],
                    [
                        'referrer_user_id' => $affRow->user_id,
                        'affiliate_code_used' => $affiliateCode,
                        'created_at' => now(),
                    ]
                );
            }
        }

        $prefData = [
            'age_min' => $onb['age_min'] ?? null,
            'age_max' => $onb['age_max'] ?? null,
            'location_radius' => $onb['location_radius'] ?? null,
        ];

        if (($onb['gender'] ?? null) === 'female') {
            $prefData['willing_to_relocate'] = $onb['willing_to_relocate'] ?? null;
        }

        $user->preferences()->create($prefData);
        // === 5. Clean up ===
        $request->session()->forget('onboarding');
        $request->session()->forget('affiliate.code');
        Auth::login($user);
        $request->session()->regenerate();

        Ga4ClientEvents::queue('sign_up', ['method' => 'email']);

        return redirect()->route('membership.education');
    }

    private function generateUniqueNickname(): string
    {
        do {
            $nickname = Str::upper(Str::random(7));
        } while (User::where('nickname', $nickname)->exists());

        return $nickname;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            Ga4ClientEvents::queue('login', ['method' => 'email']);

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Emel atau kata laluan tidak sah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
