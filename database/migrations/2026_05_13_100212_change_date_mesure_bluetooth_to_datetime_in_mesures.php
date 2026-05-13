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
        Schema::table('mesures', function (Blueprint $table) {
            $table->dateTime('date_mesure_bluetooth')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mesures', function (Blueprint $table) {
            $table->timestamp('date_mesure_bluetooth')->nullable()->change();
        });
    }
};
