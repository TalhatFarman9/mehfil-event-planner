<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $isProd = app()->environment('production');

        $users = [
            [
                'email'    => env('ADMIN_EMAIL', 'admin@mehfil.test'),
                'name'     => 'Mehfil Admin',
                'password' => env('ADMIN_PASSWORD', $isProd ? null : 'Admin@12345'),
                'role'     => 'admin',
            ],
            [
                'email'    => env('CUSTOMER_EMAIL', 'customer@mehfil.test'),
                'name'     => 'Test Customer',
                'password' => env('CUSTOMER_PASSWORD', $isProd ? null : 'Customer@12345'),
                'role'     => 'customer',
            ],
        ];

        foreach ($users as $data) {
            if (empty($data['password'])) {
                continue; // production mein env set na ho to user nahi banta
            }

            $user = User::firstOrNew(['email' => $data['email']]);

            $user->forceFill([
                'name'              => $data['name'],
                'password'          => Hash::make($data['password']),
                'role'              => $data['role'],
                'email_verified_at' => now(),
            ])->save();
        }

        $this->call(DemoBookingSeeder::class);
    }
}