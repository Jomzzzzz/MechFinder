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
        Schema::table('dispatch_requests', function (Blueprint $table) {
            // Drop deprecated motor columns (replaced by vehicle_make_model and vehicle_variant_color)
            if (Schema::hasColumn('dispatch_requests', 'motor_name')) {
                $table->dropColumn('motor_name');
            }
            if (Schema::hasColumn('dispatch_requests', 'motor_brand')) {
                $table->dropColumn('motor_brand');
            }
            if (Schema::hasColumn('dispatch_requests', 'motor_color')) {
                $table->dropColumn('motor_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_requests', function (Blueprint $table) {
            // Add back deprecated columns for rollback
            $table->string('motor_name')->nullable();
            $table->string('motor_brand')->nullable();
            $table->string('motor_color')->nullable();
        });
    }
};
