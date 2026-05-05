# MechFinder - Shops Table Analysis Report

## Summary
The `shops` database table is heavily used throughout the MechFinder application but **is NOT created by any Laravel migration file**. This is a critical gap that needs to be addressed.

---

## Files Referencing the Shops Table

### 1. **Migration Files**
- [database/migrations/2026_03_18_140000_create_shop_messages_table.php](database/migrations/2026_03_18_140000_create_shop_messages_table.php)
  - Line 18: Foreign key constraint references `shops` table
  - Syntax: `$table->foreignId('shop_id')->constrained('shops')->onDelete('cascade')`

### 2. **Controller Files**

#### [app/Http/Controllers/Auth/AuthController.php](app/Http/Controllers/Auth/AuthController.php)
- Line 57: **Creates shop records on first Google OAuth login**
  ```php
  $shopId = DB::table('shops')->insertGetId([
      'shop_name' => $googleUser->getName() . "'s Shop",
      'email' => $googleUser->getEmail(),
      'phone' => null,
      'location' => 'Olongapo City',
      'latitude' => 14.8386,
      'longitude' => 120.2842,
      'status' => 'closed',
      'rating' => 5.0,
      'created_at' => now(),
      'updated_at' => now(),
  ]);
  ```

#### [app/Http/Controllers/ShopController.php](app/Http/Controllers/ShopController.php)
- **Multiple operations on shops table:**
  - Line 24: Lookup shop by `owner_id` (fallback method)
  - Line 38: Retrieve full shop record by ID
  - Line 144: Get shop data for dashboard map
  - Line 252-263: **Update shop settings** (shop_name, address, latitude, longitude, phone, email, status, location)
  - Line 433: Get shop record for settings page
  - Line 437-442: Toggle status between open/closed

#### [app/Http/Controllers/MotoristController.php](app/Http/Controllers/MotoristController.php)
- Line 36-46: **Query shops table with these fields:**
  ```php
  DB::table('shops')
      ->select([
          'shops.id',
          'shops.shop_name',
          'shops.address',
          'shops.latitude',
          'shops.longitude',
          'shops.status',
          'shops.rating',
          'shops.phone',
          'shops.email'
      ])
  ```

### 3. **Route Files**
- [routes/web.php](routes/web.php)
  - Line 39: `/shops` - List all shops for motorists
  - Line 40: `/shops/{id}` - Show single shop details
  - Line 53: `/motorist/shops` - API endpoint for shops
  - Line 62: `/motorist/shops-for-messaging` - Get shops for messaging interface

---

## Shops Table Schema

### Table Name
`shops`

### Columns (Identified from Code Usage)

| Column | Type | Nullable | Details |
|--------|------|----------|---------|
| `id` | BIGINT | NO | Primary key, Auto-increment |
| `shop_name` | VARCHAR(150) | NO | Required in AuthController & ShopController |
| `email` | VARCHAR(255) | YES | Nullable, set from Google OAuth |
| `phone` | VARCHAR(50) | YES | Nullable, editable in settings |
| `address` | VARCHAR(255) | NO | Required in ShopController update |
| `latitude` | DECIMAL(10,8) | NO | Required in ShopController update |
| `longitude` | DECIMAL(11,8) | NO | Required in ShopController update |
| `status` | ENUM('open','busy','closed') | NO | Default 'closed', toggleable via API |
| `rating` | DECIMAL(2,1) | YES | Displayed in MotoristController queries |
| `location` | VARCHAR(255) | YES | Duplicate of address (redundant) |
| `owner_id` | BIGINT | YES | Foreign key to users table (optional) |
| `created_at` | TIMESTAMP | YES | Auto-managed by Laravel |
| `updated_at` | TIMESTAMP | YES | Auto-managed by Laravel |

---

## Related Tables

### shop_messages Table
- Foreign Key: `shop_id` → `shops.id`
- Cascading Delete: Yes
- Purpose: Direct messaging between motorists and shops

---

## Key Issues & Recommendations

### ⚠️ Issue 1: Missing Migration
**Problem:** No Laravel migration file exists to create the `shops` table.

**Current Workaround:** The table is presumably created manually in MySQL or via database dump.

**Recommendation:** Create migration file [SUGGESTED BELOW](#suggested-migration-file)

### ⚠️ Issue 2: Redundant Column
The `location` column appears to duplicate the `address` column. Both are set to the same value in ShopController line 263.

**Recommendation:** Remove `location` column and update code to use only `address`.

### ⚠️ Issue 3: Missing Shop_id in Users Table
The code references `users.shop_id` but this isn't created by any migration. Check if this column exists in the users table.

---

## Suggested Migration File

Create a new file: `database/migrations/2026_03_18_130000_create_shops_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name', 150);
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->enum('status', ['open', 'busy', 'closed'])->default('closed');
            $table->decimal('rating', 3, 1)->nullable();
            
            // Optional: remove if redundant
            $table->string('location')->nullable();
            
            // Reference to shop owner
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status']);
            $table->index(['owner_id']);
            $table->spatial('location_point')->nullable()->comment('Spatial index for geosearch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
```

---

## Controller Methods Using Shops Table

### Update Shop Settings
- **File:** ShopController.php
- **Method:** `update(Request $request)`
- **Fields Updated:** shop_name, address, latitude, longitude, phone, email, status, location

### Toggle Shop Status
- **File:** ShopController.php
- **Method:** `toggleStatus()`
- **Action:** Switches status between 'open' and 'closed'

### Get Current Shop
- **File:** ShopController.php
- **Method:** `getShop()`
- **Returns:** Shop object by ID

### Rank/List Shops
- **File:** MotoristController.php
- **Method:** `rankShops($lat, $lng)`
- **Purpose:** Returns shops ranked by distance for motorist views

---

## Next Steps

1. **Create the missing migration** using the suggested code above
2. **Run migration:** `php artisan migrate`
3. **Add `shop_id` column to users table** if not already present
4. **Remove redundant `location` column** or consolidate it with `address`
5. **Add any missing indexes** for geolocation queries (latitude/longitude)
6. **Test all CRUD operations** on shops through the admin panel

---

## Database Connection
- **Type:** MySQL
- **Host:** 127.0.0.1
- **Database:** mechfinder_db
- **Configured in:** `.env` file
