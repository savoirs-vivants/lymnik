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
        Schema::table('capteurs', function (Blueprint $table) {
            $table->string('devEUI')->nullable()->unique();
            $table->string('UID')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capteurs', function (Blueprint $table) {
            $table->dropColumn(['devEUI', 'UID']);
        });
    }
};
