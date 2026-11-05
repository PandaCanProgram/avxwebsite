<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alpha_registrations', function (Blueprint $table): void {
            $table->string('name', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('alpha_registrations', function (Blueprint $table): void {
            $table->dropColumn('name');
        });
    }
};
