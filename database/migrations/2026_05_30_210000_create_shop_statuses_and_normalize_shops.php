<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    // ── 1. Create shop_statuses lookup table ─────────────────────────────
    Schema::create("shop_statuses", function (Blueprint $table) {
      $table->id();
      $table->string("slug", 30)->unique();
      $table->string("label", 50);
      $table->string("color", 10); // dot / text colour  e.g. #2fb344
      $table->string("bg", 10); // pill background    e.g. #d1f7d6
      $table->string("next_label", 50); // confirm button label
      $table->string("next_color", 10); // confirm button colour
      $table->unsignedBigInteger("toggles_to_id")->nullable(); // self-ref (set after inserts)
      $table->unsignedTinyInteger("sort_order")->default(0);
      $table->timestamps();
    });

    // ── 2. Seed the four statuses ────────────────────────────────────────
    DB::table("shop_statuses")->insert([
      [
        "slug" => "open",
        "label" => "Open",
        "color" => "#2fb344",
        "bg" => "#d1f7d6",
        "next_label" => "Close Shop",
        "next_color" => "#d63939",
        "toggles_to_id" => null,
        "sort_order" => 1,
        "created_at" => now(),
        "updated_at" => now(),
      ],
      [
        "slug" => "busy",
        "label" => "Busy",
        "color" => "#f76707",
        "bg" => "#ffe4cc",
        "next_label" => "Set Open",
        "next_color" => "#2fb344",
        "toggles_to_id" => null,
        "sort_order" => 2,
        "created_at" => now(),
        "updated_at" => now(),
      ],
      [
        "slug" => "maintenance",
        "label" => "Maintenance",
        "color" => "#206bc4",
        "bg" => "#daeeff",
        "next_label" => "Set Open",
        "next_color" => "#2fb344",
        "toggles_to_id" => null,
        "sort_order" => 3,
        "created_at" => now(),
        "updated_at" => now(),
      ],
      [
        "slug" => "closed",
        "label" => "Closed",
        "color" => "#d63939",
        "bg" => "#fde8e8",
        "next_label" => "Open Shop",
        "next_color" => "#2fb344",
        "toggles_to_id" => null,
        "sort_order" => 4,
        "created_at" => now(),
        "updated_at" => now(),
      ],
    ]);

    // ── 3. Resolve IDs, then set toggles_to_id ──────────────────────────
    $openId = DB::table("shop_statuses")->where("slug", "open")->value("id");
    $closedId = DB::table("shop_statuses")
      ->where("slug", "closed")
      ->value("id");

    DB::table("shop_statuses")
      ->where("slug", "open")
      ->update(["toggles_to_id" => $closedId]);
    DB::table("shop_statuses")
      ->where("slug", "busy")
      ->update(["toggles_to_id" => $openId]);
    DB::table("shop_statuses")
      ->where("slug", "maintenance")
      ->update(["toggles_to_id" => $openId]);
    DB::table("shop_statuses")
      ->where("slug", "closed")
      ->update(["toggles_to_id" => $openId]);

    // ── 4. Add status_id FK column to shops (nullable for migration) ─────
    Schema::table("shops", function (Blueprint $table) use ($closedId) {
      $table->unsignedBigInteger("status_id")->nullable()->after("status");
      $table->foreign("status_id")->references("id")->on("shop_statuses");
    });

    // ── 5. Populate status_id from existing string values ────────────────
    $statuses = DB::table("shop_statuses")->get(["id", "slug"]);
    foreach ($statuses as $row) {
      DB::table("shops")
        ->where("status", $row->slug)
        ->update(["status_id" => $row->id]);
    }

    // Any rows with unknown/null status default to closed
    DB::table("shops")
      ->whereNull("status_id")
      ->update(["status_id" => $closedId]);

    // ── 6. Make status_id NOT NULL ───────────────────────────────────────
    Schema::table("shops", function (Blueprint $table) {
      $table->unsignedBigInteger("status_id")->nullable(false)->change();
    });

    // ── 7. Drop the old string status column ─────────────────────────────
    Schema::table("shops", function (Blueprint $table) {
      $table->dropColumn("status");
    });
  }

  public function down(): void
  {
    // ── 1. Re-add the string status column (nullable for back-fill) ──────
    Schema::table("shops", function (Blueprint $table) {
      $table->string("status", 30)->nullable()->after("status_id");
    });

    // ── 2. Back-fill string values from status_id ────────────────────────
    $statuses = DB::table("shop_statuses")->get(["id", "slug"]);
    foreach ($statuses as $row) {
      DB::table("shops")
        ->where("status_id", $row->id)
        ->update(["status" => $row->slug]);
    }
    DB::table("shops")
      ->whereNull("status")
      ->update(["status" => "closed"]);

    // ── 3. Drop FK + status_id ───────────────────────────────────────────
    Schema::table("shops", function (Blueprint $table) {
      $table->dropForeign(["status_id"]);
      $table->dropColumn("status_id");
    });

    // ── 4. Drop lookup table ─────────────────────────────────────────────
    Schema::dropIfExists("shop_statuses");
  }
};
