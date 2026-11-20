<?php

namespace Database\Seeders;

use App\Models\MessageGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageGroupSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local') || MessageGroup::where('name', 'Sample team chat')->exists()) {
            return;
        }

        $admin = User::where('is_admin', true)->first();
        $members = User::where('is_admin', false)->limit(3)->get();

        if ($admin && $members->isNotEmpty()) {
            MessageGroup::factory()->for($admin, 'creator')->hasAttached($members, [], 'members')
                ->create(['name' => 'Sample team chat']);
        }
    }
}
