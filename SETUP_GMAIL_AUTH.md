# MechFinder Gmail OAuth Setup Guide

## Overview
MechFinder shop admin portal now includes Gmail OAuth authentication. This provides secure, passwordless login for shop owners.

## Features
- ✅ Gmail-only authentication (@gmail.com)
- ✅ Automatic shop creation on first login
- ✅ Secure token storage (OAuth token + refresh token)
- ✅ One-click login with Google
- ✅ Auto-logout and session management
- ✅ User profile with email display

## Setup Instructions

### Step 1: Install Socialite
```bash
cd c:\xampp\htdocs\Mechfinder
composer require laravel/socialite
```

### Step 2: Create Google OAuth Credentials

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/

2. **Create a New Project**
   - Click "Select a Project" → "NEW PROJECT"
   - Name: "MechFinder"
   - Click "CREATE"

3. **Enable Google+ API**
   - Search for "Google+ API"
   - Click on it and select "ENABLE"

4. **Create OAuth 2.0 Credentials**
   - Go to "Credentials" (left sidebar)
   - Click "+ CREATE CREDENTIALS"
   - Select "OAuth client ID"
   - If prompted, configure the OAuth consent screen:
     - User type: "External"
     - Fill in app name: "MechFinder"
     - Add your email as test user
     - Save

5. **Create OAuth Client**
   - Application type: "Web application"
   - Name: "MechFinder Shop Admin"
   - Authorized JavaScript origins:
     ```
     http://localhost
     http://localhost:8000
     http://localhost:3000
     ```
   - Authorized redirect URIs:
     ```
     http://localhost/auth/google/callback
     http://localhost:8000/auth/google/callback
     ```
   - Click "CREATE"

6. **Copy Credentials**
   - Copy "Client ID"
   - Copy "Client Secret"
   - Keep these safe!

### Step 3: Configure Environment Variables

Edit `.env` file in project root:

```env
# Add these lines
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URL=http://localhost/auth/google/callback
```

**For Production:**
```env
GOOGLE_CLIENT_ID=your_production_client_id
GOOGLE_CLIENT_SECRET=your_production_client_secret
GOOGLE_REDIRECT_URL=https://yourdomain.com/auth/google/callback
```

### Step 4: Run Database Migration

```bash
php artisan migrate
```

This creates new columns in `users` table:
- `google_id` - Unique Google identifier
- `google_token` - OAuth access token
- `google_refresh_token` - OAuth refresh token
- `shop_id` - Associated shop (auto-created)
- `role` - User role (admin/motorist)

## Testing

### Local Testing
```bash
# Start development server
php artisan serve

# Visit
http://localhost:8000
```

### Test Flow
1. Click "Shop Owner Login" on welcome page
2. Click "Continue with Gmail"
3. Select your Gmail account (must be @gmail.com)
4. Authorize the app
5. Should redirect to `/shop/dashboard`
6. Check header for profile dropdown
7. Click logout to test

## API Endpoints

### Authentication Routes
- `GET /` - Welcome page
- `GET /login` - Login form
- `GET /auth/google` - Redirect to Google OAuth
- `GET /auth/google/callback` - OAuth callback handler
- `POST /logout` - Logout user

### Protected Routes (Require Login)
All `/shop/*` routes require authentication:
- `/shop/dashboard`
- `/shop/requests`
- `/shop/jobs`
- `/shop/reviews`
- `/shop/analytics`
- `/shop/settings`
- `/shop/messages`

## Database Schema Changes

### users table columns added
```sql
ALTER TABLE users ADD google_id VARCHAR(255) UNIQUE;
ALTER TABLE users ADD google_token LONGTEXT;
ALTER TABLE users ADD google_refresh_token LONGTEXT;
ALTER TABLE users ADD shop_id BIGINT UNSIGNED;
ALTER TABLE users ADD role VARCHAR(20) DEFAULT 'admin';
ALTER TABLE users ADD FOREIGN KEY (shop_id) REFERENCES shops(id);
```

## Features Auto-Enabled After Login

### Shop Initialization
When a new admin logs in for the first time:
1. User record created with Google ID
2. Shop automatically created with name: "{User Name}'s Shop"
3. Shop set to closed status by default
4. Shop location: Olongapo City
5. Shop rating: 5.0
6. User linked to shop

### Dashboard Access
After successful login, admin can:
- ✅ View real-time dispatch requests
- ✅ Toggle shop open/closed status
- ✅ Accept/decline requests
- ✅ Track active jobs
- ✅ View reviews and analytics
- ✅ Manage settings
- ✅ Message motorists

## Troubleshooting

### "Only Gmail accounts allowed"
- Make sure you're using @gmail.com address
- Other Google accounts won't work

### "OAuth callback failed"
- Verify GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env
- Check redirect URI matches exactly in Google Console
- Clear browser cookies and cache

### "Migration failed"
- Make sure database exists
- Run: `php artisan migrate:fresh` (caution: resets DB)
- Check database credentials in .env

### "User profile dropdown not showing"
- Clear browser cache
- Make sure you're logged in (check Auth::user())
- Check header.blade.php includes Auth import

## Security Considerations

### ✅ Implemented
- Gmail-only authentication (no password needed)
- OAuth tokens stored securely
- CSRF protection on all forms
- Session-based authentication
- Redirect to login on unauthorized access

### 🔒 Recommended for Production
- Use HTTPS only
- Add rate limiting to login endpoint
- Implement 2FA
- Add login activity logging
- Set `SameSite=Strict` on cookies
- Regular token refresh

## File Structure

```
app/Http/Controllers/Auth/
  └── AuthController.php

config/
  └── services.php (Google config added)

database/migrations/
  └── 2026_03_18_120000_add_google_oauth_to_users_table.php

resources/views/auth/
  └── login.blade.php

resources/views/
  ├── welcome.blade.php
  └── components/header.blade.php (logout added)

routes/
  └── web.php (auth routes added)
```

## Support

For issues or questions:
1. Check Socialite documentation: https://laravel.com/docs/socialite
2. Verify Google OAuth setup
3. Check Laravel logs: `storage/logs/`
4. Test with another Gmail account

## What's Next?

- [ ] Motorist authentication (separate from admin)
- [ ] Password reset for non-OAuth users
- [ ] Shop profile completion form
- [ ] Two-factor authentication
- [ ] Login history and analytics
- [ ] Email notifications

---

Last Updated: March 18, 2026
Version: 1.0
