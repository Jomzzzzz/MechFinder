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
    // 1. Admin
    // -------------------------------------------------------
    DB::table("users")->updateOrInsert(
      ["email" => "admin@mechfinder.test"],
      [
        "name" => "MechFinder Admin",
        "password" => Hash::make("password"),
        "role" => "admin",
        "shop_id" => null,
        "created_at" => now(),
        "updated_at" => now(),
      ]
    );

    // -------------------------------------------------------
    // 2. Shops + owners + mechanics
    // -------------------------------------------------------
    $shops = [
      [
        "shop" => [
          "shop_name" => "FastFix Moto Repair",
          "address" => "12 Rizal Ave, Olongapo City",
          "phone" => "09171234567",
          "email" => "fastfix@mechfinder.test",
          "latitude" => "14.8386",
          "longitude" => "120.2842",
          "location" => "Olongapo City",
          "status" => "open",
        ],
        "owner" => [
          "name" => "Carlos Reyes",
          "email" => "fastfix@mechfinder.test",
        ],
        "mechanics" => [
          [
            "name" => "Marco Santos",
            "email" => "marco@mechfinder.test",
            "plate" => "ABC 1234",
            "phone" => "09181111111",
          ],
          [
            "name" => "Lito Flores",
            "email" => "lito@mechfinder.test",
            "plate" => "DEF 5678",
            "phone" => "09182222222",
          ],
        ],
      ],
      [
        "shop" => [
          "shop_name" => "BikePro Service Center",
          "address" => "45 Magsaysay Dr, Olongapo City",
          "phone" => "09279876543",
          "email" => "bikepro@mechfinder.test",
          "latitude" => "14.8300",
          "longitude" => "120.2900",
          "location" => "Olongapo City",
          "status" => "open",
        ],
        "owner" => [
          "name" => "Ana Villanueva",
          "email" => "bikepro@mechfinder.test",
        ],
        "mechanics" => [
          [
            "name" => "Jose Dela Cruz",
            "email" => "jose@mechfinder.test",
            "plate" => "GHI 9012",
            "phone" => "09193333333",
          ],
        ],
      ],
      [
        "shop" => [
          "shop_name" => "SpeedWrench Garage",
          "address" => "78 Gordon Ave, Olongapo City",
          "phone" => "09351122334",
          "email" => "speedwrench@mechfinder.test",
          "latitude" => "14.8450",
          "longitude" => "120.2800",
          "location" => "Olongapo City",
          "status" => "closed",
        ],
        "owner" => [
          "name" => "Ramon Torres",
          "email" => "speedwrench@mechfinder.test",
        ],
        "mechanics" => [
          [
            "name" => "Dennis Garcia",
            "email" => "dennis@mechfinder.test",
            "plate" => "JKL 3456",
            "phone" => "09204444444",
          ],
          [
            "name" => "Ricky Mendoza",
            "email" => "ricky@mechfinder.test",
            "plate" => "MNO 7890",
            "phone" => "09205555555",
          ],
          [
            "name" => "Erwin Castillo",
            "email" => "erwin@mechfinder.test",
            "plate" => "PQR 1122",
            "phone" => "09206666666",
          ],
        ],
      ],
    ];

    foreach ($shops as $entry) {
      // Upsert shop
      $existingShop = DB::table("shops")
        ->where("email", $entry["shop"]["email"])
        ->first();

      if ($existingShop) {
        $shopId = $existingShop->id;
        DB::table("shops")
          ->where("id", $shopId)
          ->update(array_merge($entry["shop"], ["updated_at" => now()]));
      } else {
        $shopId = DB::table("shops")->insertGetId(
          array_merge($entry["shop"], [
            "created_at" => now(),
            "updated_at" => now(),
          ])
        );
      }

      // Upsert shop owner
      DB::table("users")->updateOrInsert(
        ["email" => $entry["owner"]["email"]],
        [
          "name" => $entry["owner"]["name"],
          "password" => Hash::make("password"),
          "role" => "shop",
          "shop_id" => $shopId,
          "created_at" => now(),
          "updated_at" => now(),
        ]
      );

      $ownerId = DB::table("users")
        ->where("email", $entry["owner"]["email"])
        ->value("id");
      DB::table("shops")
        ->where("id", $shopId)
        ->update(["owner_id" => $ownerId]);

      // Upsert mechanics
      foreach ($entry["mechanics"] as $mech) {
        DB::table("users")->updateOrInsert(
          ["email" => $mech["email"]],
          [
            "name" => $mech["name"],
            "password" => Hash::make("password"),
            "role" => "mechanic",
            "shop_id" => $shopId,
            "created_at" => now(),
            "updated_at" => now(),
          ]
        );

        $mechUserId = DB::table("users")
          ->where("email", $mech["email"])
          ->value("id");

        $existingProfile = DB::table("mechanic_profiles")
          ->where("user_id", $mechUserId)
          ->first();

        if ($existingProfile) {
          DB::table("mechanic_profiles")
            ->where("user_id", $mechUserId)
            ->update([
              "shop_id" => $shopId,
              "plate_number" => $mech["plate"],
              "phone" => $mech["phone"],
              "status" => "available",
              "updated_at" => now(),
            ]);
        } else {
          DB::table("mechanic_profiles")->insert([
            "user_id" => $mechUserId,
            "shop_id" => $shopId,
            "plate_number" => $mech["plate"],
            "phone" => $mech["phone"],
            "status" => "available",
            "created_at" => now(),
            "updated_at" => now(),
          ]);
        }
      }
    }

    // -------------------------------------------------------
    // 3. Test motorist
    // -------------------------------------------------------
    DB::table("users")->updateOrInsert(
      ["email" => "motorist@mechfinder.test"],
      [
        "name" => "Test Motorist",
        "password" => Hash::make("password"),
        "role" => "motorist",
        "shop_id" => null,
        "created_at" => now(),
        "updated_at" => now(),
      ]
    );
  }
}
