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
    Schema::create("shops", function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger("owner_id")->nullable();
      $table->string("shop_name");
      $table->string("address")->nullable();
      $table->string("phone", 20)->nullable();
      $table->string("email")->nullable()->unique();
      $table->string("latitude", 50)->nullable();
      $table->string("longitude", 50)->nullable();
      $table->string("location")->nullable();
      $table
        ->enum("status", ["open", "busy", "closed", "maintenance"])
        ->default("closed");
      $table->timestamps();

      $table
        ->foreign("owner_id")
        ->references("id")
        ->on("users")
        ->onDelete("set null");
      $table->index("owner_id");
      $table->index("status");
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists("shops");
  }
};
