<?php

namespace Database\Seeders;

use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local') || JobOrder::where('title', 'Sample job order')->exists()) {
            return;
        }

        $admin = User::where('is_admin', true)->first();
        $members = User::where('is_admin', false)->limit(2)->get();

        if ($admin && $members->isNotEmpty()) {
            JobOrder::factory()->for($admin, 'creator')->hasAttached($members, [], 'assignees')->create([
                'title' => 'Sample job order',
                'description' => 'Review the project requirements and prepare an initial implementation plan.',
            ]);
        }
    }
}
