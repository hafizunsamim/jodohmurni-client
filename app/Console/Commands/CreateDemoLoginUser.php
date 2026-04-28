<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Cipta atau reset akaun demo untuk login (hanya nilai dari .env).
 * Guna: php artisan jodohmurni:demo-login
 *
 * Tetapkan DEMO_LOGIN_EMAIL dan DEMO_LOGIN_PASSWORD dalam .env (jangan commit).
 */
class CreateDemoLoginUser extends Command
{
    protected $signature = 'jodohmurni:demo-login';

    protected $description = 'Cipta atau reset kata laluan akaun demo (mengikut DEMO_LOGIN_* dalam .env)';

    public function handle(): int
    {
        $email = (string) env('DEMO_LOGIN_EMAIL', '');
        $password = (string) env('DEMO_LOGIN_PASSWORD', '');

        if ($email === '' || $password === '') {
            $this->error('Tetapkan DEMO_LOGIN_EMAIL dan DEMO_LOGIN_PASSWORD dalam .env sebelum menjalankan arahan ini.');

            return self::FAILURE;
        }

        try {
            User::updateOrCreate(
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
            $this->warn('Percubaan pertama gagal: ' . $e->getMessage());
            User::updateOrCreate(
                ['email' => strtolower($email)],
                [
                    'name' => 'Admin JodohMurni',
                    'password' => Hash::make($password),
                ]
            );
        }

        $this->info('Akaun demo sedia untuk login.');
        $this->line('Email:    ' . $email);
        $this->line('Password: (nilai dari DEMO_LOGIN_PASSWORD dalam .env)');
        $this->newLine();
        $this->info('Pergi ke ' . route('login') . ' dan log masuk.');

        return self::SUCCESS;
    }
}
