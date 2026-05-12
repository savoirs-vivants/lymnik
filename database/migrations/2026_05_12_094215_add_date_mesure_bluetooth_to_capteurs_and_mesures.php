<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capteurs', function (Blueprint $table) {
            $table->timestamp('date_mesure_bluetooth')->nullable();
        });

        Schema::table('mesures', function (Blueprint $table) {
            $table->timestamp('date_mesure_bluetooth')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('capteurs', function (Blueprint $table) {
            $table->dropColumn('date_mesure_bluetooth');
        });

        Schema::table('mesures', function (Blueprint $table) {
            $table->dropColumn('date_mesure_bluetooth');
        });
    }
};
