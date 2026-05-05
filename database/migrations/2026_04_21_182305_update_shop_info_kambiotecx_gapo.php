<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update KambioTecx Gapo shop information
        DB::table('shops')
            ->where('email', 'budoycazenas@gmail.com')
            ->update([
                'shop_name' => 'KambioTecx Gapo',
                'address' => '87 Lower Kalaklan, Olongapo, Philippines, 2200',
                'phone' => '0917 140 3498',
                'email' => 'budoycazenas@gmail.com',
                'latitude' => 14.8295, // Coordinates for Lower Kalaklan, Olongapo
                'longitude' => 120.2815,
                'location' => '87 Lower Kalaklan, Olongapo, Philippines, 2200',
                'updated_at' => now()
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes - you may want to keep a backup of original values
        // This is a data migration, so we'll just skip the down() for safety
    }
};
