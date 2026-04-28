<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\EarlyBirdService;
use Illuminate\Http\Request;

class EarlyBirdAdminController extends Controller
{
    public function index()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return redirect()->back()->withErrors(['earlybird' => 'Jadual settings tidak wujud. Jalankan migration.']);
        }

        $svc = new EarlyBirdService();
        $active = $svc->isEarlyBirdActive();
        $running = $svc->isEarlyBirdRunning();
        $subscriberCount = $svc->getEarlyBirdSubscriberCount();
        $maxUsers = $svc->getMaxEarlyBirdUsers();

        return view('admin.earlybird', [
            'isActive' => $active,
            'isRunning' => $running,
            'subscriberCount' => $subscriberCount,
            'maxUsers' => $maxUsers,
        ]);
    }

    public function toggle(Request $request)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return back()->withErrors(['earlybird' => 'Jadual settings tidak wujud.']);
        }

        $value = $request->boolean('active', false);
        Setting::set(EarlyBirdService::SETTING_ACTIVE, $value ? '1' : '0');

        return redirect()->route('admin.earlybird.index')
            ->with('success', $value ? 'Early Bird diaktifkan.' : 'Early Bird dimatikan.');
    }
}
