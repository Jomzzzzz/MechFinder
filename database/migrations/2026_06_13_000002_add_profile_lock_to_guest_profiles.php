<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_profiles', function (Blueprint $table) {
            $table->boolean('profile_locked')->default(false)->after('plate_temp_number');
            $table->boolean('profile_change_requested')->default(false)->after('profile_locked');
            $table->string('change_request_reason', 500)->nullable()->after('profile_change_requested');
        });
    }

    public function down(): void
    {
        Schema::table('guest_profiles', function (Blueprint $table) {
            $table->dropColumn(['profile_locked', 'profile_change_requested', 'change_request_reason']);
        });
    }
};
