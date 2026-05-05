# MechFinder Deployment Checklist

## Before Pushing to GitHub

- [ ] Update `.env.example` with required variables
- [ ] Remove `.env` from git (already in .gitignore)
- [ ] Run `npm run build` locally to test build
- [ ] Run `composer install` to verify dependencies

## Railway Deployment Steps

1. [ ] Create GitHub repository
2. [ ] Push code to GitHub (see README.md)
3. [ ] Sign up at railway.app
4. [ ] Connect GitHub account
5. [ ] Create project from GitHub repo
6. [ ] Railway auto-detects Laravel and sets up
7. [ ] Add MySQL database (Railway will auto-configure DB_* variables)
8. [ ] Set environment variables:
   - APP_DEBUG=false
   - APP_ENV=production
   - GOOGLE_CLIENT_ID=your_value
   - GOOGLE_CLIENT_SECRET=your_value

9. [ ] Deploy (happens automatically on GitHub push)
10. [ ] SSH into Railway and run:
    ```
    php artisan migrate --force
    php artisan db:seed (if needed)
    ```

## Post-Deployment

- [ ] Test motorist app on production domain
- [ ] Test shop portal
- [ ] Verify messaging works
- [ ] Test PWA installation (should show standalone prompt)
- [ ] Test offline functionality
- [ ] Monitor logs for errors

## Custom Domain (Optional)

If you want to use your own domain:
1. Buy domain (Namecheap, GoDaddy, etc)
2. Go to Railway project settings
3. Add custom domain
4. Railway provides DNS records
5. Add DNS records to your registrar

## Database Backups

Railway provides automated backups. To download:
1. Go to MySQL service in Railway
2. Click Data tab
3. Download backup

## Support

- Railway docs: https://docs.railway.app
- Laravel deployment: https://laravel.com/docs/deployment
