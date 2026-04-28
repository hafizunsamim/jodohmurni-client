<?php

namespace App\Console;

use App\Console\Commands\SendUnreadMessageEmails;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        SendUnreadMessageEmails::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // kalau nak auto jalan setiap minit:
        // $schedule->command('chat:send-unread-emails')->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
