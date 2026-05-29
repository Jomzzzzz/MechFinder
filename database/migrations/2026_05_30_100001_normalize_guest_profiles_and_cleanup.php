<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create guest_profiles – one row per guest_token, stores identity info
        Schema::create("guest_profiles", function (Blueprint $table) {
            $table->id();
            $table->string("guest_token", 100)->unique();
            $table->string("owner_name", 150)->nullable();
            $table->string("contact_number", 50)->nullable();
            $table->timestamps();
        });

        // 2. Migrate existing guest identity data from dispatch_requests
        if (Schema::hasColumn("dispatch_requests", "owner_name")) {
            DB::statement("
                INSERT INTO guest_profiles (guest_token, owner_name, contact_number, created_at, updated_at)
                SELECT
                    guest_token,
                    MIN(owner_name),
                    MIN(contact_number),
                    MIN(created_at),
                    MAX(updated_at)
                FROM dispatch_requests
                WHERE guest_token IS NOT NULL AND guest_token != ''
                GROUP BY guest_token
                ON DUPLICATE KEY UPDATE
                    owner_name     = VALUES(owner_name),
                    contact_number = VALUES(contact_number),
                    updated_at     = VALUES(updated_at)
            ");

            // 3. Backfill guest_name from owner_name so COALESCE queries keep working
            DB::statement("
                UPDATE dispatch_requests
                SET guest_name = owner_name
                WHERE guest_name IS NULL AND owner_name IS NOT NULL
            ");

            // 4. Drop guest identity columns from dispatch_requests
            Schema::table("dispatch_requests", function (Blueprint $table) {
                $table->dropColumn(["owner_name", "contact_number"]);
            });
        }

        // 5. Drop the calculated distance column (never read from DB; computed in PHP)
        if (Schema::hasColumn("dispatch_requests", "distance")) {
            Schema::table("dispatch_requests", function (Blueprint $table) {
                $table->dropColumn("distance");
            });
        }

        // 6. Drop redundant columns from reviews (derivable via dispatch_id join)
        $reviewDrops = [];
        if (Schema::hasColumn("reviews", "owner_name")) {
            $reviewDrops[] = "owner_name";
        }
        if (Schema::hasColumn("reviews", "guest_token")) {
            $reviewDrops[] = "guest_token";
        }
        if (!empty($reviewDrops)) {
            Schema::table("reviews", function (Blueprint $table) use ($reviewDrops) {
                $table->dropColumn($reviewDrops);
            });
        }
    }

    public function down(): void
    {
        // Restore reviews columns
        Schema::table("reviews", function (Blueprint $table) {
            $table->string("owner_name", 150)->nullable()->after("dispatch_id");
            $table->string("guest_token", 100)->nullable()->after("owner_name");
        });

        // Restore dispatch_requests columns
        Schema::table("dispatch_requests", function (Blueprint $table) {
            $table->decimal("distance", 8, 2)->nullable();
            $table->string("owner_name", 150)->nullable();
            $table->string("contact_number", 50)->nullable();
        });

        // Drop guest_profiles
        Schema::dropIfExists("guest_profiles");
    }
};
