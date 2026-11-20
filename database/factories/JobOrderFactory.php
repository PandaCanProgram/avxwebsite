<?php

namespace Database\Factories;

use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobOrder>
 */
class JobOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array{created_by: UserFactory, title: string, description: string, due_date: string}
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory()->administrator(),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'due_date' => now()->addWeek()->format('Y-m-d'),
        ];
    }

    public function withAssignees(int $count = 1): static
    {
        return $this->hasAttached(User::factory()->count($count)->state(['position' => 'Software']), [], 'assignees');
    }
}
