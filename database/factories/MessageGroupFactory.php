<?php

namespace Database\Factories;

use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MessageGroup> */
class MessageGroupFactory extends Factory
{
    /** @return array{created_by: UserFactory, name: string} */
    public function definition(): array
    {
        return ['created_by' => User::factory()->administrator(), 'name' => fake()->words(3, true)];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (MessageGroup $group): void {
            $group->members()->syncWithoutDetaching([$group->created_by]);
        });
    }

    public function withMembers(int $count = 1): static
    {
        return $this->hasAttached(User::factory()->count($count)->state(['position' => 'Software']), [], 'members');
    }

    public function direct(): static
    {
        return $this->for(User::factory(), 'recipient')->state(['name' => 'Private conversation'])
            ->afterCreating(function (MessageGroup $group): void {
                $group->members()->syncWithoutDetaching([$group->direct_user_id]);
            });
    }
}
