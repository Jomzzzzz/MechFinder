# Google OAuth Configuration
# Get these from: https://console.cloud.google.com/

GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URL=http://localhost/auth/google/callback

# NOTE: To set up Google OAuth:
# 1. Go to https://console.cloud.google.com/
# 2. Create a new project
# 3. Enable Google+ API
# 4. Create OAuth 2.0 credentials (Web application)
# 5. Add authorized redirect URIs:
#    - http://localhost/auth/google/callback
#    - https://yourdomain.com/auth/google/callback
# 6. Copy Client ID and Client Secret below
