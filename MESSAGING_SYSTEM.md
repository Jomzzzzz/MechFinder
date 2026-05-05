# MechFinder Motorist-to-Shop Messaging System

## ✅ SYSTEM OVERVIEW

The messaging system enables direct communication between motorists and shops:
- **Motorist → Shop**: Send messages from the shop details modal
- **Shop → Motorist**: Reply to motorist messages through the dashboard
- **Guest Support**: Works for both authenticated users and guest motorists (with tokens)

---

## 📱 MOTORIST MESSAGING

### How Motorists Send Messages

1. **Open Shop Details Modal**
   - Browse shops on the map
   - Click "VIEW" on any shop
   - Shop details modal appears

2. **Click "Messages" Tab**
   - Switch to the Messages tab in the modal
   - See conversation history

3. **Send a Message**
   - Type message in the input field
   - Click "Send" button
   - Message is sent immediately

### Technical Flow

**Frontend (shops.blade.php)**:
```javascript
// User clicks send
sendMessage(e) → 
  POST /api/motorist/shop-messages
  {
    shop_id: currentShopId,
    message: "message text",
    guest_token: "mf_xyz..." // Auto-included from localStorage
  }
```

**Backend (MotoristController)**:
```php
sendShopMessage()
  ↓
  Validates: shop_id, message, guest_token
  ↓
  Creates shop_messages entry with:
  - motorist_id (if logged in) OR guest_token (if guest)
  - shop_id
  - message text
  - sender_type = 'motorist'
  - is_read = false
  ↓
  Returns success response
```

### Message Loading

**Frontend (shops.blade.php)**:
```javascript
// Every 2 seconds
loadModalMessages(shopId) →
  GET /api/motorist/shop-messages/{shopId}?guest_token=xyz
```

**Backend (MotoristController)**:
```php
getShopMessages($shopId)
  ↓
  Receives: shopId, guest_token (from query param)
  ↓
  Queries messages where:
  - shop_id matches
  - motorist_id OR guest_token matches
  ↓
  Returns all messages (motorist & shop)
  ↓
  Marks shop messages as read
```

---

## 🏪 SHOP MESSAGING

### How Shops Reply to Messages

1. **Go to Shop Dashboard**
   - Login to shop portal
   - Navigate to "Messages" section

2. **Select a Motorist**
   - See list of motorists who messaged
   - Click on motorist name

3. **View Conversation**
   - See all messages from that motorist
   - See all replies from shop

4. **Send Reply**
   - Type message in input field
   - Click "Send Reply" button
   - Message is sent immediately

### Technical Flow

**Frontend (shop/messages.blade.php)** - TBD:
```javascript
selectMotorист(motoristId) →
  GET /api/shop/motorist-messages/{motoristId}
```

**Backend (ShopController)**:
```php
getMotorristMessages($motoristId)
  ↓
  Validates: current shop (authenticated)
  ↓
  Returns conversation with specific motorist
  ↓
  Marks motorist messages as read
```

**Sending Reply**:
```php
sendMotorristMessage()
  ↓
  Validates: motorist_id, message
  ↓
  Creates shop_messages entry:
  - motorist_id
  - shop_id (current authenticated shop)
  - message text
  - sender_type = 'shop'
  - is_read = false
```

---

## 🗄️ DATABASE SCHEMA

### shop_messages Table

```sql
id (PRIMARY KEY)
motorist_id (BIGINT, NULLABLE) → references users
guest_token (VARCHAR 100, NULLABLE) → for guest motorists
shop_id (BIGINT) → references shops
message (TEXT)
sender_type (ENUM: 'motorist', 'shop')
is_read (BOOLEAN, default: false)
created_at
updated_at
```

### Key Constraints
- Either `motorist_id` OR `guest_token` must be populated
- `sender_type` determines who sent the message
- `is_read` tracks if shop has read motorist messages
- Indexes on (motorist_id, shop_id), (shop_id, created_at), (motorist_id, created_at)

---

## 🔄 MESSAGE FLOW EXAMPLES

### Example 1: Guest Motorist Messages Shop

**Step 1**: Guest opens motorist app
- `getGuestIdentity()` → creates `mf_xyz123456` token
- Stored in localStorage

**Step 2**: Guest opens shop details modal
- Modal loads with `currentShopId = 5`

**Step 3**: Guest types and sends message "Is your shop open?"
```
POST /api/motorist/shop-messages
{
  shop_id: 5,
  message: "Is your shop open?",
  guest_token: "mf_xyz123456"
}
```

**Step 4**: Backend creates message
```
INSERT INTO shop_messages VALUES (
  motorist_id: NULL,
  guest_token: "mf_xyz123456",
  shop_id: 5,
  message: "Is your shop open?",
  sender_type: "motorist",
  is_read: false
)
```

**Step 5**: Shop owner logs in and sees message
- Shop dashboard shows unread messages
- Clicking motorist shows conversation
- Can reply back

---

### Example 2: Shop Owner Replies to Guest

**Step 1**: Shop owner clicks on "Guest-XYZ" in messages list

**Step 2**: System fetches conversation
```
GET /api/shop/motorist-messages/NULL?guest_token=mf_xyz123456
```

**Step 3**: Shop owner types and sends "Yes, we're open until 8 PM"
```
POST /api/shop/motorist-messages
{
  motorist_id: NULL,  // or could be guest_token
  message: "Yes, we're open until 8 PM"
}
```

**Step 4**: Backend creates shop reply
```
INSERT INTO shop_messages VALUES (
  motorist_id: NULL,
  guest_token: "mf_xyz123456",
  shop_id: 5,
  message: "Yes, we're open until 8 PM",
  sender_type: "shop",
  is_read: false
)
```

**Step 5**: Guest motorist sees reply (within 2 seconds)
- Modal auto-refreshes every 2 seconds
- New message from shop appears in chat

---

## 🛠️ API ENDPOINTS

### Motorist Endpoints

**Load Messages**
```
GET /api/motorist/shop-messages/{shopId}?guest_token=xyz
```
Response:
```json
{
  "success": true,
  "shop": { id, shop_name, address, ... },
  "messages": [
    { id, motorist_id, guest_token, message, sender_type, created_at, is_read }
  ],
  "current_user_type": "guest|motorist"
}
```

**Send Message**
```
POST /api/motorist/shop-messages
Content-Type: application/json
X-CSRF-TOKEN: token
X-Guest-Token: mf_xyz
{
  "shop_id": 5,
  "message": "text",
  "guest_token": "mf_xyz"
}
```
Response:
```json
{
  "success": true,
  "message": "Message sent successfully",
  "message_id": 123
}
```

### Shop Endpoints (Protected)

**Get Motorist Conversation**
```
GET /api/shop/motorist-messages/{motoristId}
```
Response:
```json
{
  "success": true,
  "motorist": { id, name, email },
  "messages": [
    { id, motorist_id, message, sender_type, created_at, is_read }
  ],
  "shop_id": 5
}
```

**Send Reply to Motorist**
```
POST /api/shop/motorist-messages
Content-Type: application/json
X-CSRF-TOKEN: token
{
  "motorist_id": 42,
  "message": "text"
}
```
Response:
```json
{
  "success": true,
  "message": "Reply sent successfully",
  "message_id": 124
}
```

---

## 🐛 FIXES APPLIED

### Issue 1: Motorist Messages Not Sending
**Cause**: `sendShopMessage()` only accepted authenticated users
**Fix**: Added support for `guest_token` parameter
**Code**: MotoristController::sendShopMessage()

### Issue 2: Messages Not Loading in Modal
**Cause**: `loadModalMessages()` didn't pass guest token
**Fix**: Updated frontend to include `guest_token` in query param
**Code**: shops.blade.php::loadModalMessages()

### Issue 3: Guest Motorists Couldn't Send Messages
**Cause**: `shop_messages` table lacked `guest_token` column
**Fix**: Created migration to add `guest_token` column and make `motorist_id` nullable
**File**: database/migrations/2026_03_18_170000_add_guest_token_to_shop_messages.php

### Issue 4: Shop Couldn't Reply to Guests
**Cause**: No endpoint for shops to send messages
**Fix**: Added `sendMotorristMessage()` and `getMotorristMessages()` to ShopController
**Code**: ShopController::sendMotorristMessage() & getMotorristMessages()

### Issue 5: Shop Info Not Displayed Properly
**Cause**: API response didn't include full shop data
**Fix**: Both endpoints now return complete shop information
**Code**: MotoristController::getShopMessages()

---

## ✅ VERIFICATION CHECKLIST

**Motorist Messaging**:
- [ ] Guest motorist can see shop list
- [ ] Guest motorist can open shop details modal
- [ ] Guest motorist can click "Messages" tab
- [ ] Guest motorist can send a message
- [ ] Messages appear in real-time (within 2 seconds)
- [ ] Shop name and info visible in modal
- [ ] Guest identity (display name) preserved

**Shop Messaging**:
- [ ] Shop owner can see messages section
- [ ] Shop owner can see list of motorists who messaged
- [ ] Shop owner can click on motorist name
- [ ] Conversation history displays
- [ ] Shop owner can type and send reply
- [ ] Reply appears in motorist's modal (within 2 seconds)
- [ ] Message sender identification clear (motorist name or "Guest-XXXX")

**Database**:
- [ ] shop_messages table has guest_token column
- [ ] motorist_id is nullable
- [ ] Indexes are in place
- [ ] Old messages still work (backward compatible)

---

## 🚀 TESTING

### Manual Test Scenario

1. **Guest Motorist**:
   - Open `http://192.168.100.90/Mechfinder/public/motorist/shops`
   - Browse to any shop
   - Click "VIEW"
   - Modal opens with Details tab
   - Click "Messages" tab
   - Type "Hello, are you open?"
   - Hit Send
   - Message appears immediately

2. **Shop Owner**:
   - Login to shop portal
   - Go to Messages
   - See "Guest-XXXX" in message list
   - Click to view conversation
   - Type "Yes, we are open!"
   - Send reply

3. **Back to Motorist**:
   - Wait 2 seconds (auto-refresh)
   - Shop's reply appears in modal
   - Can continue conversation

---

## 📝 NEXT STEPS

1. **Run Migration** (if not auto-applied):
   ```bash
   php add_guest_token_to_messages.php
   OR
   php artisan migrate
   ```

2. **Test on Production**:
   - Deploy changes
   - Test guest messaging
   - Test shop replies
   - Monitor error logs

3. **Future Enhancements**:
   - Notification when motorist receives reply
   - Message search
   - Typing indicators
   - Photo/file sharing
   - Message delivery status (sent/delivered/read)
   - WebSocket for real-time instead of polling
