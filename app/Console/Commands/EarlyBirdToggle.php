<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\EarlyBirdService;
use Illuminate\Console\Command;

class EarlyBirdToggle extends Command
{
    protected $signature = 'earlybird:toggle {--on : Aktifkan Early Bird} {--off : Matikan Early Bird}';

    protected $description = 'Tukar status Early Bird (is_early_bird_active). Contoh: php82 artisan earlybird:toggle --on';

    public function handle(): int
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $this->error('Jadual settings tidak wujud. Jalankan migration dahulu.');
            return self::FAILURE;
        }

        $on = $this->option('on');
        $off = $this->option('off');

        if ($on && $off) {
            $this->error('Pilih salah satu: --on atau --off.');
            return self::FAILURE;
        }
        if (!$on && !$off) {
            $current = Setting::get(EarlyBirdService::SETTING_ACTIVE, '0');
            $this->info('Status Early Bird sekarang: ' . ($current === '1' ? 'AKTIF' : 'Tidak aktif'));
            $this->line('Guna --on untuk aktifkan, --off untuk matikan.');
            return self::SUCCESS;
        }

        $value = $on ? '1' : '0';
        Setting::set(EarlyBirdService::SETTING_ACTIVE, $value);
        $this->info('Early Bird sekarang: ' . ($value === '1' ? 'AKTIF' : 'Tidak aktif'));
        return self::SUCCESS;
    }
}
