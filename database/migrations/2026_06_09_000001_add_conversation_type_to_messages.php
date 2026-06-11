<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('messages', 'conversation_type')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->string('conversation_type')->nullable()->after('sender_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('messages', 'conversation_type')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropColumn('conversation_type');
            });
        }
    }
};
