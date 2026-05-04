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
        Schema::table('campagnes', function (Blueprint $table) {
            $table->unsignedSmallInteger('nb_groupes')->default(0)->after('nom');
        });
    }

    public function down(): void
    {
        Schema::table('campagnes', function (Blueprint $table) {
            $table->dropColumn('nb_groupes');
        });
    }
};
