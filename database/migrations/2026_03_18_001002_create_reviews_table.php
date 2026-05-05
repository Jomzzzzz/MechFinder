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
    Schema::create("reviews", function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger("motorist_id")->nullable();
      $table->unsignedBigInteger("shop_id");
      $table->unsignedBigInteger("dispatch_id")->nullable();
      $table->string("guest_token", 100)->nullable();
      $table->string("owner_name", 150)->nullable();
      $table->integer("rating")->default(5);
      $table->text("comment")->nullable();
      $table->timestamps();

      $table
        ->foreign("motorist_id")
        ->references("id")
        ->on("users")
        ->onDelete("set null");
      $table
        ->foreign("shop_id")
        ->references("id")
        ->on("shops")
        ->onDelete("cascade");
      $table
        ->foreign("dispatch_id")
        ->references("id")
        ->on("dispatch_requests")
        ->onDelete("set null");
      $table->index("motorist_id");
      $table->index("shop_id");
      $table->index("dispatch_id");
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists("reviews");
  }
};
