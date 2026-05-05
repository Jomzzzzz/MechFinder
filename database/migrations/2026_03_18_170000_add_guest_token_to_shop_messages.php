<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table("shop_messages", function (Blueprint $table) {
      // Add guest_token support for messages from guest motorists
      if (!Schema::hasColumn("shop_messages", "guest_token")) {
        $table->string("guest_token")->nullable()->after("motorist_id");
      }

      // Make motorist_id nullable since guests don't have accounts
      DB::statement(
        "ALTER TABLE shop_messages MODIFY motorist_id BIGINT UNSIGNED NULL"
      );
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table("shop_messages", function (Blueprint $table) {
      if (Schema::hasColumn("shop_messages", "guest_token")) {
        $table->dropColumn("guest_token");
      }
    });
  }
};
