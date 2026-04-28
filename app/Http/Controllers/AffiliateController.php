<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProRequest;
use App\Services\AffiliateTierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateController extends Controller
{
    public function submitProRequest(Request $request, AffiliateTierService $tiers)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $profile = $tiers->profileFor($user);
        if (!$profile['enabled']) {
            abort(403, 'Affiliate tidak tersedia untuk keahlian anda.');
        }
        if ($profile['tier'] !== AffiliateTierService::TIER_STANDARD) {
            abort(403, 'Permohonan Affiliate Pro tidak diperlukan.');
        }

        // block if pending exists
        $hasPending = AffiliateProRequest::query()
            ->where('user_id', $user->id)
            ->where('status', AffiliateProRequest::STATUS_PENDING)
            ->exists();
        if ($hasPending) {
            return back()->withErrors(['reason' => 'Anda sudah mempunyai permohonan yang masih pending.']);
        }

        $data = $request->validate([
            'promotion_platform' => ['required', 'string', 'min:2', 'max:120'],
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $platform = trim((string) ($data['promotion_platform'] ?? ''));
        $reason = trim((string) $data['reason']);
        if ($platform === '') {
            return back()->withErrors(['promotion_platform' => 'Platform promosi wajib diisi.']);
        }
        if ($reason === '') {
            return back()->withErrors(['reason' => 'Sebab permohonan wajib diisi.']);
        }

        AffiliateProRequest::create([
            'user_id' => $user->id,
            'reason' => $reason,
            'promotion_platform' => $platform,
            'status' => AffiliateProRequest::STATUS_PENDING,
        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Permohonan Affiliate Pro telah dihantar. Status: Pending.');
    }
}

