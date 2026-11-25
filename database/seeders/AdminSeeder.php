<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew([
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        ]);

        $user->forceFill([
            'name'              => 'Admin',
            'password'          => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe123!')),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ])->save();
    }
}