<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void
  {
    // Rename old 'user' role to 'motorist'
    DB::table("users")
      ->where("role", "user")
      ->update(["role" => "motorist"]);
  }

  public function down(): void
  {
    DB::table("users")
      ->where("role", "motorist")
      ->update(["role" => "user"]);
  }
};
