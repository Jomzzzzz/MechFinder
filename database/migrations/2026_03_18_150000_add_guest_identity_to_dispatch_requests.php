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
            // Add guest identification columns if they don't exist
            if (!Schema::hasColumn('dispatch_requests', 'guest_token')) {
                $table->string('guest_token')->nullable()->after('motorist_id');
            }
            
            if (!Schema::hasColumn('dispatch_requests', 'guest_name')) {
                $table->string('guest_name')->nullable()->after('guest_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_requests', function (Blueprint $table) {
            if (Schema::hasColumn('dispatch_requests', 'guest_token')) {
                $table->dropColumn('guest_token');
            }
            if (Schema::hasColumn('dispatch_requests', 'guest_name')) {
                $table->dropColumn('guest_name');
            }
        });
    }
};
