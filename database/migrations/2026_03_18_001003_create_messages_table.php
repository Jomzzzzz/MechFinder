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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('motorist_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->enum('sender_type', ['motorist', 'shop'])->default('motorist');
            $table->timestamps();
            
            $table->foreign('motorist_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->index('motorist_id');
            $table->index('shop_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
