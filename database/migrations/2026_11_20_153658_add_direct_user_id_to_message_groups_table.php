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
        Schema::table('message_groups', function (Blueprint $table) {
            $table->foreignId('direct_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->unique(['created_by', 'direct_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_groups', function (Blueprint $table) {
            $table->dropUnique(['created_by', 'direct_user_id']);
            $table->dropConstrainedForeignId('direct_user_id');
        });
    }
};
