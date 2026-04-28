<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCommission;
use App\Models\UserAffiliateCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalAffiliateDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if (empty($user->is_external_affiliate)) {
            abort(403);
        }

        $base = AffiliateCommission::query()->where('referrer_user_id', $user->id);

        $successfulSubscribers = (clone $base)
            ->distinct('referred_user_id')
            ->count('referred_user_id');

        $pendingSen = (int) (clone $base)->where('status', AffiliateCommission::STATUS_PENDING)->sum('commission_sen');
        $paidSen = (int) (clone $base)->where('status', AffiliateCommission::STATUS_PAID)->sum('commission_sen');
        $rejectedSen = (int) (clone $base)->where('status', AffiliateCommission::STATUS_REJECTED)->sum('commission_sen');

        $totalSen = $pendingSen + $paidSen + $rejectedSen;

        $aff = UserAffiliateCode::query()->where('user_id', $user->id)->first();
        $affiliateCode = $aff ? (string) $aff->code : null;
        $affiliateClicks = $aff ? (int) ($aff->clicks ?? 0) : 0;

        $baseUrl = rtrim((string) config('app.url'), '/');
        $affiliateLink = $affiliateCode ? ($baseUrl . '/?ref=' . $affiliateCode) : null;

        return view('affiliate.external_dashboard', [
            'successfulSubscribers' => $successfulSubscribers,
            'pendingSen' => $pendingSen,
            'paidSen' => $paidSen,
            'rejectedSen' => $rejectedSen,
            'totalSen' => $totalSen,
            'affiliateLink' => $affiliateLink,
            'affiliateClicks' => $affiliateClicks,
        ]);
    }
}

