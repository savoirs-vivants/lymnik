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
        Schema::create('mesures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('capteur_id')->constrained('capteurs')->onDelete('cascade');

            $table->float('turbidite')->nullable();
            $table->float('conductivite')->nullable();
            $table->float('temp_eau')->nullable();
            $table->float('hauteur')->nullable();
            $table->float('debit')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesures');
    }
};
