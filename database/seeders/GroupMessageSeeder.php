<?php

namespace Database\Seeders;

use App\Models\GroupMessage;
use App\Models\MessageGroup;
use Illuminate\Database\Seeder;

class GroupMessageSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $group = MessageGroup::where('name', 'Sample team chat')->first();

        if ($group && ! $group->messages()->exists()) {
            GroupMessage::factory()->for($group, 'group')->create([
                'user_id' => $group->created_by,
                'body' => 'Welcome to the team chat. Share project updates here.',
            ]);
        }
    }
}
