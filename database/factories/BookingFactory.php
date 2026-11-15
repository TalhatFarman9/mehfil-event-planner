<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'client_name' => fake()->name(),
        'client_email' => fake()->safeEmail(),
        'client_phone' => '03'.fake()->numerify('#########'),
        'event_type' => fake()->randomElement(['Wedding', 'Mehndi', 'Walima', 'Birthday', 'Corporate']),
        'event_date' => fake()->dateTimeBetween('-1 month', '+3 months')->format('Y-m-d'),
        'guests' => fake()->numberBetween(20, 500),
        'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled']),
        'notes' => fake()->optional()->sentence(),
    ];
}
}
