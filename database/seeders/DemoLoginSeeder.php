<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Cipta satu akaun untuk login ke sistem (nilai dari .env sahaja).
 *
 * Tetapkan DEMO_LOGIN_EMAIL dan DEMO_LOGIN_PASSWORD dalam .env, kemudian:
 *   php artisan db:seed --class=DemoLoginSeeder
 */
class DemoLoginSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('DEMO_LOGIN_EMAIL', '');
        $password = (string) env('DEMO_LOGIN_PASSWORD', '');

        if ($email === '' || $password === '') {
            if ($this->command) {
                $this->command->warn('DemoLoginSeeder dilangkau: tetapkan DEMO_LOGIN_EMAIL dan DEMO_LOGIN_PASSWORD dalam .env.');
            }

            return;
        }

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

        if ($this->command) {
            $this->command->info('Akaun demo telah disedia.');
            $this->command->info('Email: ' . $email);
        }
    }
}
