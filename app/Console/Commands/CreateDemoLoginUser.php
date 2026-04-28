<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Cipta atau reset akaun demo untuk login.
 * Guna: php artisan jodohmurni:demo-login
 *
 * Login dengan:
 *   Email:    hafizunsamim@gmail.com
 *   Password: JodohMurni2025!
 */
class CreateDemoLoginUser extends Command
{
    protected $signature = 'jodohmurni:demo-login';

    protected $description = 'Cipta atau reset kata laluan akaun demo (hafizunsamim@gmail.com)';

    public function handle(): int
    {
        $email = 'hafizunsamim@gmail.com';
        $password = 'JodohMurni2025!';

        // updateOrCreate: jika user wujud, password akan di-update; jika tidak, user baru dicipta.
        try {
            $user = User::updateOrCreate(
                ['email' => strtolower($email)],
                [
                    'name' => 'Admin JodohMurni',
                    'password' => Hash::make($password),
                    'nickname' => 'ADMIN01',
                    'phone' => '+60123456789',
                    'gender' => 'male',
                    'path' => 'monogami',
                ]
            );
        } catch (\Throwable $e) {
            // Jika jadual users hanya ada name, email, password (migration asas)
            $this->warn('Percubaan pertama gagal: ' . $e->getMessage());
            $user = User::updateOrCreate(
                ['email' => strtolower($email)],
                [
                    'name' => 'Admin JodohMurni',
                    'password' => Hash::make($password),
                ]
            );
        }

        $this->info('Akaun demo sedia untuk login.');
        $this->line('Email:    ' . $email);
        $this->line('Password: ' . $password);
        $this->newLine();
        $this->info('Pergi ke ' . route('login') . ' dan log masuk dengan maklumat di atas.');

        return self::SUCCESS;
    }
}
