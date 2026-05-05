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
        // Drop unused Laravel framework tables
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
        
        // Drop legacy messaging table (replaced by shop_messages)
        Schema::dropIfExists('messages');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: These tables are part of framework cleanup
        // Rollback not recommended as they are unused dependencies
    }
};
