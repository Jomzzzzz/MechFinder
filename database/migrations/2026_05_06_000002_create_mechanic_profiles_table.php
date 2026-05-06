<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create("mechanic_profiles", function (Blueprint $table) {
      $table->id();
      $table->foreignId("user_id")->constrained()->cascadeOnDelete();
      $table->foreignId("shop_id")->constrained()->cascadeOnDelete();
      $table->string("plate_number")->nullable();
      $table->string("phone", 20)->nullable();
      $table->string("photo")->nullable();
      $table
        ->enum("status", ["available", "dispatched", "off_duty"])
        ->default("available");
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists("mechanic_profiles");
  }
};
