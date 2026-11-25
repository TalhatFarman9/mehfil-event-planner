<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Seeder;

class DemoBookingSeeder extends Seeder
{
    public function run(): void
    {
        if (Booking::count() === 0) {
            Booking::factory()->count(35)->create();
        }

        // jin bookings ka amount abhi 0 hai unko amount aur pichle 6 mahine ki date do
        Booking::where('amount', '<=', 0)->get()->each(function ($b) {
            $b->forceFill([
                'amount'     => rand(50, 400) * 1000,
                'event_date' => now()->subMonths(rand(0, 5))->startOfMonth()->addDays(rand(0, 27))->toDateString(),
            ])->save();
        });
    }
}