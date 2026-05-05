<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    // -------------------------------------------------------
    // 1. Admin user (no shop)
    // -------------------------------------------------------
    DB::table("users")->updateOrInsert(
      ["email" => "admin@mechfinder.test"],
      [
        "name" => "MechFinder Admin",
        "email" => "admin@mechfinder.test",
        "password" => Hash::make("password"),
        "role" => "admin",
        "shop_id" => null,
        "created_at" => now(),
        "updated_at" => now(),
      ]
    );

    // -------------------------------------------------------
    // 2. Shop owner — create shop first, then user
    // -------------------------------------------------------
    $existingShop = DB::table("shops")
      ->where("email", "shop@mechfinder.test")
      ->first();

    if ($existingShop) {
      $shopId = $existingShop->id;
    } else {
      $shopId = DB::table("shops")->insertGetId([
        "shop_name" => "Test Repair Shop",
        "address" => "123 Main St, Test City",
        "phone" => "09171234567",
        "email" => "shop@mechfinder.test",
        "latitude" => "14.5995",
        "longitude" => "120.9842",
        "location" => "Manila",
        "status" => "open",
        "created_at" => now(),
        "updated_at" => now(),
      ]);
    }

    $shopOwnerId = DB::table("users")->updateOrInsert(
      ["email" => "shop@mechfinder.test"],
      [
        "name" => "Shop Owner",
        "email" => "shop@mechfinder.test",
        "password" => Hash::make("password"),
        "role" => "shop",
        "shop_id" => $shopId,
        "created_at" => now(),
        "updated_at" => now(),
      ]
    );

    $shopOwnerId = DB::table("users")
      ->where("email", "shop@mechfinder.test")
      ->value("id");

    // Link shop owner_id back to the user
    DB::table("shops")
      ->where("id", $shopId)
      ->update(["owner_id" => $shopOwnerId]);

    // -------------------------------------------------------
    // 3. Regular motorist / user (no shop)
    // -------------------------------------------------------
    DB::table("users")->insertOrIgnore([
      "name" => "Test Motorist",
      "email" => "user@mechfinder.test",
      "password" => Hash::make("password"),
      "role" => "user",
      "shop_id" => null,
      "created_at" => now(),
      "updated_at" => now(),
    ]);
  }
}
