<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KpiBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $statuses = ['pending', 'confirmed', 'completed', 'confirmed', 'completed'];
    $events   = ['Wedding', 'Mehndi', 'Walima', 'Birthday', 'Corporate Dinner'];

    for ($i = 0; $i < 60; $i++) {
        $date = now()->subMonths(rand(0, 5))->startOfMonth()->addDays(rand(0, 27));

        Booking::create([
            'client_name' => fake()->name(),
            'event_date'  => $date->toDateString(),
            'status'      => $statuses[array_rand($statuses)],
            'amount'      => rand(50, 400) * 1000,
            // + tumhare table ke baaki required columns (event_type, guests, etc.)
        ]);
    }
}
}
