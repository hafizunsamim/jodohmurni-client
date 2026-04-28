<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Cipta satu akaun untuk login ke sistem.
 *
 * Selepas jalankan: php artisan db:seed --class=DemoLoginSeeder
 *
 * Guna credential ini untuk login:
 *   Email:    hafizunsamim@gmail.com
 *   Password: JodohMurni2025!
 *
 * (Sila tukar kata laluan selepas login pertama.)
 */
class DemoLoginSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'hafizunsamim@gmail.com';
        $password = 'JodohMurni2025!';

        // updateOrCreate supaya kata laluan sentiasa dikemas kini (boleh login)
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

        $this->command->info('Akaun demo telah disedia.');
        $this->command->info('Email: ' . $email);
        $this->command->info('Password: ' . $password);
    }
}
