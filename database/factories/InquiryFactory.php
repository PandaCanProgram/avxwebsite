<?php

namespace Database\Factories;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inquiry>
 */
class InquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'submission_id' => fake()->uuid(),
            'kind' => 'contact',
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => 'Custom drone fabrication',
            'message' => 'We would like to discuss a custom drone for our research project.',
        ];
    }
}
