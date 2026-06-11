<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add mechanic support to messages table
        if (!Schema::hasColumn('messages', 'mechanic_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->unsignedBigInteger('mechanic_id')->nullable()->after('shop_id');
                $table->foreign('mechanic_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            });
        }

        // Add sender_name field for caching
        if (!Schema::hasColumn('messages', 'sender_name')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->string('sender_name')->nullable()->after('message');
            });
        }

        // Update sender_type to support 'mechanic'
        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender_type')->change();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['mechanic_id']);
            $table->dropColumn(['mechanic_id', 'sender_name']);
        });
    }
};
