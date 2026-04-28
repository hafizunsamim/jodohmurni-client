<?php

namespace App\Console\Commands;

use App\Services\EarlyBirdService;
use Illuminate\Console\Command;

class EarlyBirdStatus extends Command
{
    protected $signature = 'earlybird:status';

    protected $description = 'Papar status Early Bird dan tarikh tamat.';

    public function handle(): int
    {
        $svc = new EarlyBirdService();
        $active = $svc->isEarlyBirdActive();
        $running = $svc->isEarlyBirdRunning();
        $count = $svc->getEarlyBirdSubscriberCount();
        $max = $svc->getMaxEarlyBirdUsers();

        $this->table(
            ['Setting', 'Nilai'],
            [
                ['Early Bird aktif (is_early_bird_active)', $active ? 'Ya' : 'Tidak'],
                ['Bilangan subscriber early bird (HYPE)', $count . ' / ' . $max],
                ['Early Bird berjalan (aktif + subscriber < max)', $running ? 'Ya' : 'Tidak'],
            ]
        );
        return self::SUCCESS;
    }
}
