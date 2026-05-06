<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create("dispatch_mechanics", function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId("dispatch_request_id")
        ->constrained()
        ->cascadeOnDelete();
      $table->foreignId("mechanic_id")->constrained("users")->cascadeOnDelete();
      $table
        ->enum("status", ["assigned", "en_route", "arrived", "completed"])
        ->default("assigned");
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists("dispatch_mechanics");
  }
};
