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
            $table->foreignId('cours_d_eau_id')->nullable()->constrained('cours_d_eaus')->nullOnDelete()->after('long');
        });
    }

    public function down(): void
    {
        Schema::table('capteurs', function (Blueprint $table) {
            $table->dropForeign(['cours_d_eau_id']);
            $table->dropColumn('cours_d_eau_id');
        });
    }
};
