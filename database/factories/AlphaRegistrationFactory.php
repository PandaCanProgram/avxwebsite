<?php

namespace Database\Factories;

use App\Models\AlphaRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<AlphaRegistration> */
class AlphaRegistrationFactory extends Factory
{
    /** @return array{name: string, email: string, consented_at: Carbon} */
    public function definition(): array
    {
        return ['name' => fake()->name(), 'email' => fake()->unique()->safeEmail(), 'consented_at' => now()];
    }
}
