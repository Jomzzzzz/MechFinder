<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guest_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('motorist_id')->nullable()->after('guest_token');
            $table->foreign('motorist_id')->references('id')->on('users')->onDelete('set null');
            $table->index('motorist_id');
        });
    }

    public function down(): void
    {
        Schema::table('guest_profiles', function (Blueprint $table) {
            $table->dropForeign(['motorist_id']);
            $table->dropIndex(['motorist_id']);
            $table->dropColumn('motorist_id');
        });
    }
};
