<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('map_locations', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('address')->nullable();
      $table->text('description')->nullable();
      $table->decimal('latitude', 10, 7);
      $table->decimal('longitude', 10, 7);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('map_locations');
  }
};
