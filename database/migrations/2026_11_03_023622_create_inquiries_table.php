<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->uuid('submission_id')->unique();
            $table->string('kind', 20);
            $table->string('name', 100);
            $table->string('email', 254);
            $table->string('organization', 150)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('interest', 100)->nullable();
            $table->string('subject', 160);
            $table->text('message');
            $table->timestamp('team_notified_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
