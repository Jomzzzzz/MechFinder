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
            // Owner/Motorist Info
            $table->string('owner_name', 255)->nullable()->comment('Primary identifier - Motorist name');
            $table->string('contact_number', 20)->nullable()->comment('Phone number for SMS/WhatsApp notifications');
            
            // Vehicle Info
            $table->string('vehicle_make_model', 255)->nullable()->comment('Vehicle make and model (e.g., Honda Wave 110)');
            $table->string('vehicle_variant_color', 255)->nullable()->comment('Vehicle color and variant');
            $table->string('plate_temp_number', 100)->nullable()->comment('License plate or temporary number');
            
            // Keep old columns for backwards compatibility (will deprecate)
            // motor_name, motor_brand, motor_color already exist
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_requests', function (Blueprint $table) {
            $table->dropColumn([
                'owner_name',
                'contact_number',
                'vehicle_make_model',
                'vehicle_variant_color',
                'plate_temp_number'
            ]);
        });
    }
};
