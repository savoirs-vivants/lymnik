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
    Schema::create('analyses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('point_id')->constrained('points')->cascadeOnDelete();

        $table->string('type');
        $table->string('image')->nullable();
        $table->json('mesures');
        $table->boolean('est_valide')->default(false);

        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->unsignedBigInteger('participant_id')->nullable();
        $table->unsignedBigInteger('session_id')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
