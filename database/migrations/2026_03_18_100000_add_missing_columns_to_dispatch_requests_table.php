<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table("dispatch_requests", function (Blueprint $table) {
      // Add missing columns if they don't exist
      if (!Schema::hasColumn("dispatch_requests", "request_type")) {
        $table
          ->enum("request_type", ["shop", "dispatch"])
          ->default("shop")
          ->after("issue_type");
      }

      if (!Schema::hasColumn("dispatch_requests", "location")) {
        $table->string("location")->nullable()->after("request_type");
      }

      if (!Schema::hasColumn("dispatch_requests", "distance")) {
        $table->decimal("distance", 8, 2)->nullable()->after("location");
      }

      if (!Schema::hasColumn("dispatch_requests", "accepted_at")) {
        $table->timestamp("accepted_at")->nullable();
      }

      if (!Schema::hasColumn("dispatch_requests", "en_route_at")) {
        $table->timestamp("en_route_at")->nullable();
      }

      if (!Schema::hasColumn("dispatch_requests", "completed_at")) {
        $table->timestamp("completed_at")->nullable();
      }

      if (!Schema::hasColumn("dispatch_requests", "arrived_at")) {
        $table->timestamp("arrived_at")->nullable();
      }
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table("dispatch_requests", function (Blueprint $table) {
      // Drop columns if they exist
      if (Schema::hasColumn("dispatch_requests", "request_type")) {
        $table->dropColumn("request_type");
      }
      if (Schema::hasColumn("dispatch_requests", "location")) {
        $table->dropColumn("location");
      }
      if (Schema::hasColumn("dispatch_requests", "distance")) {
        $table->dropColumn("distance");
      }
      if (Schema::hasColumn("dispatch_requests", "accepted_at")) {
        $table->dropColumn("accepted_at");
      }
      if (Schema::hasColumn("dispatch_requests", "en_route_at")) {
        $table->dropColumn("en_route_at");
      }
      if (Schema::hasColumn("dispatch_requests", "arrived_at")) {
        $table->dropColumn("arrived_at");
      }
      if (Schema::hasColumn("dispatch_requests", "completed_at")) {
        $table->dropColumn("completed_at");
      }
    });
  }
};
