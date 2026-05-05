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
        // Create shop_messages table for direct messaging between motorists and shops
        Schema::create('shop_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorist_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->enum('sender_type', ['motorist', 'shop'])->default('motorist');
            $table->timestamps();
            
            // Indexes for fast lookup
            $table->index(['motorist_id', 'shop_id']);
            $table->index(['shop_id', 'created_at']);
            $table->index(['motorist_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_messages');
    }
};
