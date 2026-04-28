<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $pref = $user->preferences;

        // Jika tiada preference, cipta kosong
        if (!$pref) {
            $pref = (object) [
                'age_min' => 18,
                'age_max' => 30,
                'location_radius' => 'tak_kisah',
                'willing_to_relocate' => null,
            ];
        }

        return view('preferences.edit', compact('pref', 'user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'age_min' => 'required|integer|min:18|max:100',
            'age_max' => 'required|integer|min:18|max:100|gte:age_min',
            'location_radius' => 'required|in:50km,51-150km,151km_ke_atas,tak_kisah',
        ]);

        $user = Auth::user();
        $data = [
            'age_min' => $request->age_min,
            'age_max' => $request->age_max,
            'location_radius' => $request->location_radius,
        ];

        // Jika wanita, simpan willing_to_relocate
        if ($user->gender === 'female') {
            $request->validate([
                'willing_to_relocate' => 'required|in:ya,tidak,boleh_dipertimbangkan',
            ]);
            $data['willing_to_relocate'] = $request->willing_to_relocate;
        }

        // Simpan atau update
        if ($user->preferences) {
            $user->preferences->update($data);
        } else {
            $user->preferences()->create($data);
        }

        return redirect()->route('dashboard')->with('success', 'Preferensi jodoh berjaya dikemaskini.');
    }
}