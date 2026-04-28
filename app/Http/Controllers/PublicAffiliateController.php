<?php

namespace App\Http\Controllers;

use App\Models\ExternalAffiliateApplication;
use App\Models\User;
use Illuminate\Http\Request;

class PublicAffiliateController extends Controller
{
    public function show()
    {
        return view('affiliate.public_apply');
    }

    public function apply(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone_number' => ['required', 'string', 'max:50'],
            'promotion_platform' => ['required', 'string', 'min:2', 'max:120'],
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $fullName = trim((string) $data['full_name']);
        $email = mb_strtolower(trim((string) $data['email']));
        $platform = trim((string) ($data['promotion_platform'] ?? ''));
        $reason = trim((string) $data['reason']);

        // normalize phone: allow +, digits; strip spaces/dashes
        $phoneRaw = preg_replace('/[^0-9+]/', '', (string) $data['phone_number']);
        $phone = trim((string) $phoneRaw);

        if ($phone === '' || !preg_match('/^\\+?[0-9]{9,15}$/', $phone)) {
            return back()->withErrors([
                'phone_number' => 'Nombor telefon tidak sah. Sila guna format seperti +60123456789.',
            ])->withInput();
        }

        // Edge case A: email already exists in users
        if (User::query()->where('email', $email)->exists()) {
            return back()->withErrors([
                'email' => 'Emel ini sudah wujud dalam sistem. Sila log masuk jika anda sudah ada akaun.',
            ])->withInput();
        }

        // Edge case B/E: block if pending/approved exists; allow if rejected
        $latest = ExternalAffiliateApplication::query()
            ->where('email', $email)
            ->orderByDesc('id')
            ->first();

        if ($latest) {
            $st = strtolower((string) $latest->status);
            if ($st === ExternalAffiliateApplication::STATUS_PENDING) {
                return back()->withErrors([
                    'email' => 'Permohonan anda masih dalam semakan (pending).',
                ])->withInput();
            }
            if ($st === ExternalAffiliateApplication::STATUS_APPROVED) {
                return back()->withErrors([
                    'email' => 'Permohonan anda telah diluluskan. Sila gunakan login di bawah untuk akses dashboard.',
                ])->withInput();
            }
        }

        if ($platform === '') {
            return back()->withErrors([
                'promotion_platform' => 'Platform promosi wajib diisi.',
            ])->withInput();
        }

        ExternalAffiliateApplication::create([
            'full_name' => $fullName,
            'email' => $email,
            'phone_number' => $phone,
            'reason' => $reason,
            'promotion_platform' => $platform,
            'status' => ExternalAffiliateApplication::STATUS_PENDING,
        ]);

        return redirect()
            ->route('affiliate.public')
            ->with('success', 'Permohonan anda telah dihantar. Status: Pending (dalam semakan admin).');
    }
}

