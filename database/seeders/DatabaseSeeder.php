<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mehfil.test'],
            [
                'name' => 'Mehfil Admin',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@mehfil.test'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('Customer@12345'),
                'role' => 'customer',
            ]
        );
    }
}