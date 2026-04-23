<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cours_d_eaus', function (Blueprint $table) {
            $table->double('bbox_min_lng')->nullable()->index();
            $table->double('bbox_min_lat')->nullable()->index();
            $table->double('bbox_max_lng')->nullable()->index();
            $table->double('bbox_max_lat')->nullable()->index();
        });
    }
    public function down(): void {
        Schema::table('cours_d_eaus', function (Blueprint $table) {
            $table->dropColumn(['bbox_min_lng', 'bbox_min_lat', 'bbox_max_lng', 'bbox_max_lat']);
        });
    }
};
