<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSwipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SwipeController extends Controller
{
    public function index(Request $request)
    {
        $me = Auth::user();
        if (!$me) {
            return redirect()->route('login');
        }

        // ✅ check subscription aktif (same logic dashboard)
        $hasActiveSub = \App\Models\Subscription::where('user_id', $me->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->exists();

        // filter: all | like | dislike
        $filter = $request->get('type', 'all');
        if (!in_array($filter, ['all', 'like', 'dislike'], true)) {
            $filter = 'all';
        }

        $swipesQuery = UserSwipe::query()
            ->where('user_id', $me->id)
            ->when($filter !== 'all', function ($q) use ($filter) {
                $q->where('action', $filter);
            })
            ->orderByDesc('created_at');

        $swipes = $swipesQuery->paginate(20);

        // ambil semua target user dalam satu query
        $targetIds = $swipes->pluck('target_user_id')->unique()->values()->all();
        $targets = User::whereIn('id', $targetIds)->get()->keyBy('id');

        return view('likes', [
            'me' => $me,
            'swipes' => $swipes,
            'targets' => $targets,
            'filter' => $filter,
            'hasActiveSub' => $hasActiveSub, // ✅ pass to view
        ]);
    }

    public function store(Request $request)
    {
        $me = Auth::user();
        if (!$me) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'target_user_id' => 'required|string|size:36',
            'action' => 'required|in:like,dislike',
        ]);

        if ($data['target_user_id'] === $me->id) {
            return response()->json(['message' => 'Invalid target'], 422);
        }

        $targetExists = User::where('id', $data['target_user_id'])->exists();
        if (!$targetExists) {
            return response()->json(['message' => 'Target not found'], 404);
        }

        UserSwipe::updateOrCreate(
            ['user_id' => $me->id, 'target_user_id' => $data['target_user_id']],
            ['action' => $data['action']]
        );

        return response()->json(['ok' => true]);
    }
}
