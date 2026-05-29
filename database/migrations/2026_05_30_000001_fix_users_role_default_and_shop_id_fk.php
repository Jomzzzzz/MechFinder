<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix two DB issues:
     * 1. users.role default was 'admin' — change to 'motorist'
     * 2. users.shop_id was plain bigInteger with no FK — add proper FK constraint
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Fix dangerous default: 'admin' → 'motorist'
            $table->string('role')->default('motorist')->change();

            // Add proper FK constraint on shop_id
            $table->unsignedBigInteger('shop_id')->nullable()->change();
            $table->foreign('shop_id')
                ->references('id')
                ->on('shops')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->string('role')->default('admin')->change();
            $table->bigInteger('shop_id')->nullable()->change();
        });
    }
};
