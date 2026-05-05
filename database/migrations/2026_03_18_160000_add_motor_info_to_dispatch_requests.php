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
            // Add motor info columns if they don't exist
            if (!Schema::hasColumn('dispatch_requests', 'motor_name')) {
                $table->string('motor_name')->nullable()->after('guest_name');
            }
            
            if (!Schema::hasColumn('dispatch_requests', 'motor_brand')) {
                $table->string('motor_brand')->nullable()->after('motor_name');
            }
            
            if (!Schema::hasColumn('dispatch_requests', 'motor_color')) {
                $table->string('motor_color')->nullable()->after('motor_brand');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_requests', function (Blueprint $table) {
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
};
