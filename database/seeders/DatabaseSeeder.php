<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@mehfil.test',
                'name' => 'Mehfil Admin',
                'password' => 'Admin@12345',
                'role' => 'admin',
            ],
            [
                'email' => 'customer@mehfil.test',
                'name' => 'Test Customer',
                'password' => 'Customer@12345',
                'role' => 'customer',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrNew(['email' => $data['email']]);

            $user->forceFill([
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'email_verified_at' => now(),
            ])->save();
        }
    }
}