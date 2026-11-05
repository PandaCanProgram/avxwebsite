<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alpha_registrations', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('email', 254)->unique();
            $table->timestamp('consented_at');
            $table->timestamp('team_notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alpha_registrations');
    }
};
