<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ExternalAffiliatePasswordController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if (empty($user->is_external_affiliate)) {
            abort(403);
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ]);

        $user->password = Hash::make((string) $data['password']);
        $user->save();

        return back()->with('success', 'Password berjaya dikemaskini.');
    }
}

