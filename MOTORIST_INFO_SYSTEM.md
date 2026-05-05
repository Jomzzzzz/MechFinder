# MechFinder - Motorist Info & Messaging System

## ✅ MOTORIST BASIC INFORMATION

The system now captures comprehensive motorist and motor details:

### Motorist Information
- **Name**: Captured from guest identity system (custom display name or auto-generated Motorist-XXXX)
- **Location**: GPS coordinates (latitude, longitude)
- **Display**: Shows in shop dashboard, requests, and jobs views

### Motor Information (NEW)
- **Motor Model/Name**: e.g., "Honda Wave 110", "Yamaha Mio 125"
- **Motor Brand/Unit Brand**: e.g., "Honda", "Yamaha", "Suzuki"
- **Motor Color**: e.g., "Red", "Black", "Silver"

## 🗄️ DATABASE CHANGES

### New Migration
File: `database/migrations/2026_03_18_160000_add_motor_info_to_dispatch_requests.php`

Adds three columns to `dispatch_requests` table:
- `motor_name` (VARCHAR 255, nullable)
- `motor_brand` (VARCHAR 255, nullable)
- `motor_color` (VARCHAR 100, nullable)

**To manually add columns (if migration fails), run in tinker:**
```
DB::statement('ALTER TABLE dispatch_requests ADD COLUMN motor_name VARCHAR(255) NULL');
DB::statement('ALTER TABLE dispatch_requests ADD COLUMN motor_brand VARCHAR(255) NULL');
DB::statement('ALTER TABLE dispatch_requests ADD COLUMN motor_color VARCHAR(100) NULL');
```

## 📝 FORM CHANGES

### Motorist Dispatch Form
File: `resources/views/motorist/dispatch.blade.php`

New section added before issue type selection:
- Motor Name/Model input field
- Brand/Unit Brand input field  
- Motor Color input field

All fields are optional but recommended for better shop identification of the motorist's vehicle.

## 🎛️ CONTROLLER UPDATES

### MotoristController::createDispatchRequest()
File: `app/Http/Controllers/MotoristController.php`

**Added validation:**
```php
'motor_name' => 'nullable|string|max:255',
'motor_brand' => 'nullable|string|max:255',
'motor_color' => 'nullable|string|max:100',
```

**Added to database insert:**
- motor_name
- motor_brand
- motor_color

## 👀 SHOP DASHBOARD UPDATES

### Dashboard View
File: `resources/views/shop/dashboard.blade.php`

**Map Popup**: Now shows motor info
```
Motorist Name
🏍️ Motor Model (if provided)
🔧 Equipment Brand (if provided)
🎨 Motor Color (if provided)
Issue Type
Status
Location
```

### Shop Requests Page
File: `resources/views/shop/requests.blade.php`

Added to INFO GRID:
- Motor Model
- Motor Brand
- Motor Color
(displays only if data is provided)

### Shop Jobs Page
File: `resources/views/shop/jobs.blade.php`

Added to DETAILS section:
- Motor Model
- Motor Brand
- Motor Color
(displays only if data is provided)

### Dashboard Components
File: `resources/views/components/request-item.blade.php`

Now displays motorist name and motor info as:
```
👤 [Motorist Name]
🏍️ Model: [motor_name]
🔧 Brand: [motor_brand]
🎨 Color: [motor_color]
[Time requested]
```

## 💬 MESSAGING SYSTEM

### Motorist-to-Shop Messaging
The messaging system allows motorists to send messages to shops from the shop details modal.

**Implementation:**
- Database table: `shop_messages`
- API endpoints:
  - `GET /api/motorist/shops-for-messaging` - List shops with last message
  - `GET /api/motorist/shop-messages/{shopId}` - Get chat history
  - `POST /api/motorist/shop-messages` - Send message
- Real-time polling: 2-second refresh cycle
- Message display: Chat bubbles with timestamps

**Features:**
- Works for both authenticated users and guest motorists
- Identifies motorists by user ID or guest token
- Marks messages as read when shop views them
- Last message preview in shop list
- Unread count indicator

### Shop-to-Motorist Messaging
Shops can respond to motorist messages through the messaging interface.

**Implementation:**
- Database: Same `shop_messages` table
- API handles both motorist and shop messages
- Message sender identified by `sender_type` (guest/motorist/shop)

### Message Format
Each message includes:
- Sender identification (motorist_id, guest_token, or shop_id)
- Message content
- Timestamp
- Read status
- Sender type

## 🔍 VERIFICATION CHECKLIST

- [x] Motor fields added to dispatch form
- [x] Controller validation updated
- [x] Database migration created
- [x] Shop dashboard displays motorist name
- [x] Shop requests display motor info
- [x] Shop jobs display motor info
- [x] Map popup shows motor info
- [x] Shop messages system working
- [x] Guest identification system active
- [x] Messaging between motorist and shop enabled

## 🚀 DEPLOYMENT NOTES

When deploying to production:

1. Run migrations:
   ```bash
   php artisan migrate --force
   ```

2. This will add the motor info columns to dispatch_requests table

3. Motor info will be captured in all new dispatch requests

4. Shop dashboard will display motor info for all requests

## 📱 Testing

### On Motorist App:
1. Open dispatch form
2. Fill in motor info (optional but recommended)
3. Submit dispatch request
4. Check shop dashboard to verify motor info is displayed

### In Shop Dashboard:
1. View incoming requests
2. Check if motor info appears
3. Open job details
4. Verify motor information is visible
5. Check map popup for motor info

### Messaging:
1. Motorist: Click "Message" in shop details modal
2. Shop: View messages from motorists
3. Both: Send/receive messages in real-time
4. Verify messages display with motorist name and timestamp

## 📌 Notes

- Motor info is optional (won't break if not provided)
- Display uses conditional rendering (only shows if data exists)
- All fields nullable in database for backward compatibility
- Guest motorists can provide motor info just like authenticated users
- Motor info helps shops identify the vehicle visually
- Messaging works regardless of whether motor info is provided
