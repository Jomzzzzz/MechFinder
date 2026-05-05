# MechFinder - Comprehensive Database & Architecture Analysis
**Generated:** April 24, 2026

---

## 📋 EXECUTIVE SUMMARY

This Laravel project implements a motorist-to-shop dispatch request system with messaging and rating capabilities. The **critical issue** is that three core tables (`dispatch_requests`, `shops`, `reviews`) are referenced by multiple controllers but are NOT created by any Laravel migration file, creating a significant deployment and maintainability problem.

---

## 🗄️ DATABASE TABLES

### ✅ TABLES CREATED BY MIGRATIONS

#### 1. **users** (Primary Migration: `0001_01_01_000000_create_users_table.php`)
**Purpose:** Shop owner authentication and motorist user management

| Column | Type | Details |
|--------|------|---------|
| id | BIGINT | Auto-increment primary key |
| name | VARCHAR(255) | User/shop name |
| email | VARCHAR(255) | Unique email |
| email_verified_at | TIMESTAMP | Email verification |
| password | VARCHAR(255) | Hashed password |
| remember_token | VARCHAR(100) | Session token |
| google_id | VARCHAR(255) | Google OAuth ID (nullable, unique) |
| google_token | TEXT | Google access token |
| google_refresh_token | TEXT | Google refresh token |
| shop_id | BIGINT | Foreign key to shops (nullable) |
| role | VARCHAR(255) | User role (default: 'admin') |
| created_at, updated_at | TIMESTAMP | Timestamps |

**Migration Files:**
- `0001_01_01_000000_create_users_table.php` - Main table
- `2026_03_18_120000_add_google_oauth_to_users_table.php` - Google OAuth columns
- `2026_03_18_140000_create_shop_messages_table.php` - References via foreign key

**Usage in Controllers:**
- AuthController: User creation/authentication with Google OAuth
- ShopController: Shop owner identification via shop_id
- MotoristController: Motorist identification for dispatch requests

---

#### 2. **password_reset_tokens** (Primary Migration: `0001_01_01_000000_create_users_table.php`)
**Purpose:** Password reset functionality

| Column | Type | Details |
|--------|------|---------|
| email | VARCHAR(255) | Primary key |
| token | VARCHAR(255) | Reset token |
| created_at | TIMESTAMP | Token creation time |

**Status:** Exists but not actively used in current codebase

---

#### 3. **sessions** (Primary Migration: `0001_01_01_000000_create_users_table.php`)
**Purpose:** Laravel session management

| Column | Type | Details |
|--------|------|---------|
| id | VARCHAR(255) | Primary key (session ID) |
| user_id | BIGINT | Foreign key to users |
| ip_address | VARCHAR(45) | Client IP |
| user_agent | TEXT | Browser user agent |
| payload | LONGTEXT | Serialized session data |
| last_activity | INTEGER | Last activity timestamp |

**Status:** Used by Laravel session driver

---

#### 4. **cache** & **cache_locks** (Primary Migration: `0001_01_01_000001_create_cache_table.php`)
**Purpose:** Application caching

| Column (cache) | Type | Details |
|---|---|---|
| key | VARCHAR(255) | Primary key |
| value | MEDIUMTEXT | Cached value |
| expiration | INTEGER | Expiration timestamp |

**Status:** Framework infrastructure, likely unused in this implementation

---

#### 5. **jobs**, **job_batches**, **failed_jobs** (Primary Migration: `0001_01_01_000002_create_jobs_table.php`)
**Purpose:** Laravel queue job processing

**Status:** Framework infrastructure, appears unused (no queue jobs implemented)

---

#### 6. **shop_messages** (Migration: `2026_03_18_140000_create_shop_messages_table.php`)
**Purpose:** Direct messaging between motorists and shops

| Column | Type | Details |
|--------|------|---------|
| id | BIGINT | Auto-increment primary key |
| motorist_id | BIGINT | Foreign key to users (nullable after migration) |
| shop_id | BIGINT | Foreign key to shops (cascading delete) |
| guest_token | VARCHAR(100) | Token for guest motorists (nullable) |
| message | LONGTEXT | Message content |
| is_read | BOOLEAN | Read status (default: false) |
| sender_type | ENUM | 'motorist' or 'shop' (default: 'motorist') |
| created_at, updated_at | TIMESTAMP | Timestamps |

**Indexes:**
- `[motorist_id, shop_id]` - Fast lookup by both parties
- `[shop_id, created_at]` - Recent messages for shop
- `[motorist_id, created_at]` - Recent messages for motorist

**Modification:** `2026_03_18_170000_add_guest_token_to_shop_messages.php` added guest_token support

**Usage:**
- ShopController: Send/receive messages with motorists
- MotoristController: Send/receive messages with shops
- Supports both authenticated users and guest motorists (via tokens)

---

### ⚠️ CRITICAL: TABLES NOT CREATED BY MIGRATIONS

#### 1. **dispatch_requests** ❌ NO MIGRATION
**Purpose:** Core table for all service dispatch requests from motorists to shops

**Referenced in:** 20+ locations across controllers

| Column | Type | Details |
|--------|------|---------|
| id | BIGINT | Auto-increment primary key |
| shop_id | BIGINT | Foreign key to shops |
| motorist_id | BIGINT | Foreign key to users (nullable for guests) |
| guest_token | VARCHAR(100) | Token for guest identification |
| guest_name | VARCHAR(100) | Display name for guest |
| issue_type | VARCHAR(255) | Type of issue (e.g., "flat tire", "battery") |
| description | LONGTEXT | Detailed issue description |
| location | VARCHAR(255) | Current location of motorist |
| distance | DECIMAL(8,2) | Distance from shop (km) |
| latitude | DECIMAL(10,8) | Current latitude |
| longitude | DECIMAL(11,8) | Current longitude |
| price | DECIMAL(10,2) | Quote/final price |
| budget | DECIMAL(10,2) | Motorist budget (if provided) |
| request_type | ENUM | 'shop' or 'dispatch' |
| status | ENUM | 'requested', 'accepted', 'en_route', 'arrived', 'in_progress', 'completed', 'declined' |
| accepted_at | TIMESTAMP | When shop accepted (nullable) |
| en_route_at | TIMESTAMP | When shop started traveling (nullable) |
| arrived_at | TIMESTAMP | When shop arrived (nullable) |
| completed_at | TIMESTAMP | When job completed (nullable) |
| **LEGACY VEHICLE INFO** | | |
| motor_name | VARCHAR(255) | Old: motor model/name (kept for compatibility) |
| motor_brand | VARCHAR(255) | Old: motor brand (kept for compatibility) |
| motor_color | VARCHAR(100) | Old: motor color (kept for compatibility) |
| **NEW VEHICLE INFO** | | |
| vehicle_make_model | VARCHAR(255) | e.g., "Honda Wave 110", "Yamaha Mio 125" |
| vehicle_variant_color | VARCHAR(255) | Color and variant info |
| plate_temp_number | VARCHAR(100) | License plate or temporary number |
| **MOTORIST INFO** | | |
| owner_name | VARCHAR(255) | Motorist/vehicle owner name |
| contact_number | VARCHAR(20) | SMS/WhatsApp contact number |
| lat | DECIMAL(10,8) | Duplicate of latitude (denormalization) |
| lng | DECIMAL(11,8) | Duplicate of longitude (denormalization) |
| created_at, updated_at | TIMESTAMP | Timestamps |

**Columns Added by Migrations:**
- `price` - Migration `2026_03_18_093657_add_price_to_dispatch_requests_table.php`
- `request_type`, `location`, `distance`, `accepted_at`, `en_route_at`, `completed_at` - Migration `2026_03_18_100000_add_missing_columns_to_dispatch_requests_table.php`
- `guest_token`, `guest_name` - Migration `2026_03_18_150000_add_guest_identity_to_dispatch_requests.php`
- `motor_name`, `motor_brand`, `motor_color` - Migration `2026_03_18_160000_add_motor_info_to_dispatch_requests.php`
- `owner_name`, `contact_number`, `vehicle_make_model`, `vehicle_variant_color`, `plate_temp_number` - Migration `2026_04_16_000000_add_motorist_info_to_dispatch_requests.php`

**⚠️ ISSUE:** The base table creation migration is **MISSING**. All other migrations use `Schema::table()` to add columns to a non-existent table.

---

#### 2. **shops** ❌ NO MIGRATION
**Purpose:** Motor shop information and management

**Referenced in:** 15+ locations across controllers and API routes

| Column | Type | Details |
|--------|------|---------|
| id | BIGINT | Auto-increment primary key |
| owner_id | BIGINT | Foreign key to users (nullable) |
| shop_name | VARCHAR(150) | Shop name |
| email | VARCHAR(255) | Contact email (nullable) |
| phone | VARCHAR(50) | Contact phone (nullable) |
| address | VARCHAR(255) | Physical address |
| location | VARCHAR(255) | Duplicate of address (redundant) |
| latitude | DECIMAL(10,8) | Shop latitude for mapping |
| longitude | DECIMAL(11,8) | Shop longitude for mapping |
| status | ENUM | 'open', 'busy', or 'closed' |
| rating | DECIMAL(3,1) | Average shop rating (nullable) |
| response_time | INTEGER | Average response time in minutes (nullable) |
| created_at, updated_at | TIMESTAMP | Timestamps |

**How It's Created:** Manually in AuthController when user logs in via Google OAuth:
```php
$shopId = DB::table('shops')->insertGetId([
    'shop_name' => $googleUser->getName() . "'s Shop",
    'email' => $googleUser->getEmail(),
    // ... other fields
]);
```

**Data Migration:** `2026_04_21_182305_update_shop_info_kambiotecx_gapo.php` updates KambioTecx Gapo shop info

**⚠️ ISSUE:** No creation migration = table doesn't exist in fresh installations

---

#### 3. **reviews** ❌ NO MIGRATION
**Purpose:** Shop ratings and review system

**Referenced in:** 5+ locations (MotoristController, ShopController)

| Column | Type | Details |
|--------|------|---------|
| id | BIGINT | Auto-increment primary key |
| dispatch_id | BIGINT | Foreign key to dispatch_requests |
| motorist_id | BIGINT | Foreign key to users |
| shop_id | BIGINT | Foreign key to shops |
| rating | INTEGER | 1-5 star rating |
| comment | VARCHAR(500) | Review comment (nullable) |
| created_at, updated_at | TIMESTAMP | Timestamps |

**Usage:**
- MotoristController: `submitReview()` - Save review after job completion
- ShopController: `reviews()` - Display reviews for shop
- Average rating calculation for shop ranking

**⚠️ ISSUE:** No creation migration

---

#### 4. **messages** ❓ UNCLEAR STATUS
**Purpose:** Appears to be legacy messaging system (now replaced by `shop_messages`)

**Referenced in:** 2 locations (MotoristController)

| Column | Type (inferred) | Details |
|--------|------|---------|
| id | BIGINT | Auto-increment primary key |
| dispatch_id | BIGINT | Associated dispatch request |
| sender_id | BIGINT | User who sent message |
| message | LONGTEXT | Message content |
| is_read | BOOLEAN | Read status |
| created_at | TIMESTAMP | Creation timestamp |

**Status:** Possibly deprecated in favor of `shop_messages` but still referenced in MotoristController

**⚠️ ISSUE:** Unclear if still used; table creation unknown

---

## 🎯 ACTIVE JOBS FEATURE

### Location & Implementation
**File:** [app/Http/Controllers/ShopController.php](app/Http/Controllers/ShopController.php#L117-L133)

**Method:** `fetchActiveJobs()`

### How It Works
1. **Retrieves active jobs** from `dispatch_requests` table
2. **Status filter:** Includes only requests with status in:
   - `accepted` - Shop accepted the job
   - `en_route` - Shop is traveling to location
   - `arrived` - Shop arrived at motorist location
   - `in_progress` - Work in progress

3. **Data joined** with users table to get motorist name
4. **Returns** to view component: `components.active-jobs-list`

### Related Features

#### Dashboard Integration
- **Dashboard method:** `ShopController::dashboard()`
- **Active jobs count:** Queries dispatch_requests with active statuses
- **Displayed on:** Shop portal home page

#### Status Lifecycle
```
requested → [accept/decline] → accepted → en_route → arrived → in_progress → completed
                                                                                    ↑
                                                            [Shop marks as complete]
```

#### API for Status Updates
- **Route:** `POST /api/motorist/shop-messages` (wait, should be `/request/{id}/status`)
- **Method:** `ShopController::updateRequestStatus()`
- **Supported statuses:** All lifecycle statuses

#### Map Visualization
- **Method:** `ShopController::dashboardMapData()`
- **Shows:** Active jobs on map with GPS coordinates
- **Filters:** Include 'requested', 'accepted', 'en_route', 'arrived', 'in_progress'

### Active Jobs Metrics
**From dashboard():**
```php
$activeJobsCount = DB::table('dispatch_requests')
    ->where('shop_id', $shopId)
    ->whereIn('status', ['accepted', 'en_route', 'arrived', 'in_progress'])
    ->count();
```

### Features
- ✅ Real-time status tracking
- ✅ Map integration with GPS
- ✅ Motorist information display (name, phone, vehicle)
- ✅ Job history and archival (completed/declined)
- ✅ Quick status updates via buttons/API
- ✅ Job details modal with full information

---

## 🔍 UNUSED & DEPRECATED FEATURES

### 1. **password_reset_tokens table**
- **Status:** UNUSED
- **Reason:** Application uses Google OAuth only; no password reset implemented
- **Recommendation:** Remove from schema or implement password reset feature

### 2. **messages table** (possibly)
- **Status:** POTENTIALLY DEPRECATED
- **Reason:** `shop_messages` table seems to be the replacement
- **Evidence:** Both tables reference motorist-shop communication, but `shop_messages` is newer and includes `guest_token` support
- **Recommendation:** 
  - Verify if `messages` table is still used
  - If not, write migration to drop it
  - Update code to use `shop_messages` exclusively

### 3. **Legacy vehicle info columns in dispatch_requests**
- **Columns:** `motor_name`, `motor_brand`, `motor_color`
- **Status:** DEPRECATED in favor of new columns
- **Reason:** Newer columns added for more detailed vehicle info
  - New: `vehicle_make_model`, `vehicle_variant_color`, `plate_temp_number`
  - New: `owner_name`, `contact_number`
- **Recommendation:** 
  - Keep for backward compatibility (app does)
  - Eventually migrate data to new columns
  - Remove old columns in future major version

### 4. **Duplicate columns in dispatch_requests**
- **`lat` & `longitude`** vs **`latitude` & `longitude`**
- **Status:** DENORMALIZATION / REDUNDANCY
- **Recommendation:** Choose one naming convention and remove duplicates

### 5. **location column in shops table**
- **Status:** REDUNDANT
- **Reason:** Duplicates `address` column
- **Usage:** Both set to same value in ShopController update
- **Recommendation:** Remove and use only `address`

### 6. **Framework infrastructure tables (unused)**
- **Tables:** `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`
- **Status:** LIKELY UNUSED
- **Reason:** No queue jobs or cache operations found in codebase
- **Recommendation:** Monitor for use; remove migrations if not needed

### 7. **response_time column in shops**
- **Status:** DEFINED IN SCHEMA but NOT USED
- **Reason:** Referenced in queries but value never set
- **Recommendation:** Either implement calculation and use it, or remove

---

## 📊 TABLE RELATIONSHIPS

```
users (1) ─→ (many) shops
  ├─ shop_id foreign key
  └─ owner_id via shops.owner_id

shops (1) ─→ (many) dispatch_requests
  └─ shop_id foreign key

users (1) ─→ (many) dispatch_requests (as motorist)
  ├─ motorist_id foreign key
  └─ Also support guest motorists via guest_token

dispatch_requests (1) ─→ (many) reviews
  └─ dispatch_id foreign key

dispatch_requests (1) ─→ (many) shop_messages
  └─ Via shop_id & motorist_id

users (1) ─→ (many) shop_messages (as motorist)
  ├─ motorist_id foreign key
  └─ Also guest_token for guest motorists

shops (1) ─→ (many) shop_messages
  └─ shop_id foreign key
```

---

## ⚠️ CRITICAL ISSUES & RECOMMENDATIONS

### Issue 1: Missing Creation Migrations ⚠️ HIGH PRIORITY
**Problem:** Three critical tables have no Laravel migrations:
- `dispatch_requests` - Core business logic table
- `shops` - Core business data table
- `reviews` - Business feature (ratings)

**Impact:**
- Fresh installations will fail (tables don't exist)
- Cannot run migrations on different environments
- No version control for database schema
- Data loss risk on rollbacks

**Solution:**
Create migration files:
```bash
php artisan make:migration create_dispatch_requests_table
php artisan make:migration create_shops_table
php artisan make:migration create_reviews_table
```

### Issue 2: Manual Table Creation in Code ⚠️ MEDIUM PRIORITY
**Problem:** ShopController creates `shops` records via raw `DB::table()->insertGetId()`

**Solution:** Should use model or proper seeding

### Issue 3: Redundant/Duplicate Columns ⚠️ LOW PRIORITY
- `shops.location` duplicates `shops.address`
- `dispatch_requests.lat/lng` duplicates `latitude/longitude`

**Solution:** Normalize schema and refactor code

### Issue 4: Deprecated Columns Still Active
- Old motor info columns in dispatch_requests

**Solution:** Implement data migration and deprecation timeline

### Issue 5: Unused Framework Tables ⚠️ LOW PRIORITY
- `cache`, `jobs`, `job_batches` - Not used by application

**Solution:** Document usage or remove migrations

### Issue 6: Unclear Message System
- Two messaging tables: `messages` and `shop_messages`
- Possible overlap in functionality

**Solution:** Audit and consolidate

---

## 📱 RECENT CHANGES (from migrations)

| Date | Migration File | Changes |
|------|---|---|
| 2026-04-21 | `2026_04_21_182305_update_shop_info_kambiotecx_gapo.php` | Updated specific shop data |
| 2026-04-16 | `2026_04_16_000000_add_motorist_info_to_dispatch_requests.php` | Added owner_name, contact_number, vehicle info columns |
| 2026-03-18 | `2026_03_18_170000_add_guest_token_to_shop_messages.php` | Guest token support for messages |
| 2026-03-18 | `2026_03_18_160000_add_motor_info_to_dispatch_requests.php` | Motor info columns (legacy) |
| 2026-03-18 | `2026_03_18_150000_add_guest_identity_to_dispatch_requests.php` | Guest token/name for dispatch requests |
| 2026-03-18 | `2026_03_18_140000_create_shop_messages_table.php` | New messaging system |
| 2026-03-18 | `2026_03_18_120000_add_google_oauth_to_users_table.php` | Google authentication |
| 2026-03-18 | `2026_03_18_100000_add_missing_columns_to_dispatch_requests_table.php` | Request lifecycle columns |
| 2026-03-18 | `2026_03_18_093657_add_price_to_dispatch_requests_table.php` | Price tracking |

---

## 📝 SUMMARY TABLE

| Table | Created? | Used Actively? | Status | Priority |
|-------|----------|---|--------|----------|
| users | ✅ | ✅ | Active | - |
| password_reset_tokens | ✅ | ❌ | Unused | Low |
| sessions | ✅ | ✅ | Active | - |
| cache | ✅ | ❌ | Unused | Low |
| cache_locks | ✅ | ❌ | Unused | Low |
| jobs | ✅ | ❌ | Unused | Low |
| job_batches | ✅ | ❌ | Unused | Low |
| failed_jobs | ✅ | ❌ | Unused | Low |
| shop_messages | ✅ | ✅ | Active | - |
| **dispatch_requests** | ❌❌ | ✅✅ | **CRITICAL** | **HIGH** |
| **shops** | ❌❌ | ✅✅ | **CRITICAL** | **HIGH** |
| **reviews** | ❌❌ | ✅ | **Critical** | **HIGH** |
| messages | ❓ | ❓ | Unknown | Medium |

---

## 🔧 NEXT STEPS

1. **Create missing migrations** for dispatch_requests, shops, and reviews
2. **Refactor table creation** in AuthController to use migrations
3. **Audit and consolidate** messaging tables
4. **Normalize schema** (remove duplicates)
5. **Document deprecation** path for legacy columns
6. **Test fresh installation** to verify all tables create properly

---

Generated with Laravel code analysis - Last updated April 24, 2026
