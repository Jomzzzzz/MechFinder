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
        Schema::table('guest_profiles', function (Blueprint $table) {
            $table->string('vehicle_make_model', 150)->nullable()->after('contact_number');
            $table->string('vehicle_variant_color', 150)->nullable()->after('vehicle_make_model');
            $table->string('plate_temp_number', 80)->nullable()->after('vehicle_variant_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_profiles', function (Blueprint $table) {
            $table->dropColumn(['vehicle_make_model', 'vehicle_variant_color', 'plate_temp_number']);
        });
    }
};
