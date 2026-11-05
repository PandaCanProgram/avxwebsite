<?php

namespace Database\Seeders;

use App\Models\AlphaRegistration;
use Illuminate\Database\Seeder;

class AlphaRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            AlphaRegistration::firstOrCreate(['email' => 'alpha-demo@example.test'], ['consented_at' => now()]);
        }
    }
}
