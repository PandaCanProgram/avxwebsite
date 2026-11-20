<?php

namespace Database\Factories;

use App\Models\GroupMessage;
use App\Models\MessageGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<GroupMessage> */
class GroupMessageFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'message_group_id' => MessageGroup::factory(),
            'user_id' => fn (array $attributes): int => MessageGroup::findOrFail($attributes['message_group_id'])->created_by,
            'body' => fake()->paragraph(),
            'client_id' => (string) Str::uuid(),
        ];
    }
}
