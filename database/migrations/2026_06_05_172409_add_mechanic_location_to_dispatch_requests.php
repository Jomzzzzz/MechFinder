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
            $table->decimal('mechanic_lat', 10, 7)->nullable()->after('longitude');
            $table->decimal('mechanic_lng', 10, 7)->nullable()->after('mechanic_lat');
            $table->timestamp('mechanic_location_at')->nullable()->after('mechanic_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_requests', function (Blueprint $table) {
            $table->dropColumn(['mechanic_lat', 'mechanic_lng', 'mechanic_location_at']);
        });
    }
};
