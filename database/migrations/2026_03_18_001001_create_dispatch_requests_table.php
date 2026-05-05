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
        Schema::create('dispatch_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('motorist_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('status')->default('requested');
            $table->text('description')->nullable();
            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();
            $table->string('guest_token', 100)->nullable();
            $table->string('guest_name')->nullable();
            $table->timestamps();
            
            $table->foreign('motorist_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('set null');
            $table->index('motorist_id');
            $table->index('shop_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_requests');
    }
};
