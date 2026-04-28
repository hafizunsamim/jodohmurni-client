<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
public function index(Request $request)
{
    $me = Auth::user();

    // ✅ check subscription aktif
    $hasActiveSub = \App\Models\Subscription::where('user_id', $me->id)
        ->where('status', 'active')
        ->where(function ($q) {
            $q->whereNull('ends_at')
              ->orWhere('ends_at', '>=', now());
        })
        ->exists();

    $term = trim((string) $request->input('q', ''));
    $users = collect();

    // ✅ OPTION B: kalau tak subscribe, jangan buat query walaupun ada q dalam URL
    if ($term !== '' && $hasActiveSub) {
        $users = User::query()
            ->where('id', '!=', $me->id)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', '%'.$term.'%')
                  ->orWhere('email', 'like', '%'.$term.'%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    // kalau tak subscribe, kita kosongkan term supaya UI tunjuk empty-state
    if (!$hasActiveSub) {
        $term = '';
    }

    return view('search.index', [
        'me' => $me,
        'term' => $term,
        'users' => $users,
        'hasActiveSub' => $hasActiveSub,
    ]);
}

}
