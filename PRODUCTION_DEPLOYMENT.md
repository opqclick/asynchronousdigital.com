# Production Deployment Checklist

## ✅ Completed

### Git Merge
- [x] Merged `dev` branch into `main`
- [x] Pushed to remote repository

### Environment Configuration
- [x] Set `APP_ENV=production`
- [x] Set `APP_DEBUG=false`
- [x] Updated `APP_NAME=AsynchronousDigital`
- [x] Updated `APP_URL=https://asynchronousdigital.com`
- [x] Set `LOG_LEVEL=error` (production logging)
- [x] Set `LOG_STACK=daily` (daily log rotation)
- [x] Enabled `SESSION_ENCRYPT=true` (secure sessions)
- [x] Set `QUEUE_CONNECTION=database` (async job processing)
- [x] Disabled `TELESCOPE_ENABLED=false` (production security)

## 📋 Pre-Deployment Tasks

### Server Setup
- [ ] Ensure production server has PHP 8.2+, MySQL, Composer, Node.js
- [ ] Configure web server (Nginx/Apache) with proper root to `/public`
- [ ] Set proper file permissions (storage and bootstrap/cache writable)
- [ ] Install SSL certificate for HTTPS

### Database
- [ ] Create production database
- [ ] Update `.env` with production database credentials:
  - `DB_HOST` (production database host)
  - `DB_DATABASE` (production database name)
  - `DB_USERNAME` (production database user)
  - `DB_PASSWORD` (production database password)
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed essential data if needed

### Mail Configuration
- [ ] Update mail settings in `.env`:
  - `MAIL_MAILER` (smtp, mailgun, ses, etc.)
  - `MAIL_HOST`
  - `MAIL_PORT`
  - `MAIL_USERNAME`
  - `MAIL_PASSWORD`
  - `MAIL_FROM_ADDRESS`
  - `MAIL_FROM_NAME`

### Digital Ocean Spaces (already configured)
- [x] Spaces credentials configured
- [x] Region: sgp1
- [x] Bucket: asynchronousdigitalcloudstorage

### Security
- [ ] Generate new `APP_KEY` for production: `php artisan key:generate`
- [ ] Review and update CORS settings if needed
- [ ] Configure firewall rules
- [ ] Set up fail2ban or similar intrusion prevention
- [ ] Enable rate limiting in production

### Optimization
- [ ] Clear and cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Build production assets: `npm run build`

### Queue Workers
- [ ] Set up supervisor to run queue workers
- [ ] Configure queue worker in supervisor config
- [ ] Start supervisor and verify workers are running

### Monitoring & Logs
- [ ] Set up log rotation
- [ ] Configure error tracking (Sentry, Bugsnag, etc.)
- [ ] Set up uptime monitoring
- [ ] Configure backup strategy for database and files

### DNS & Domain
- [ ] Point domain to production server IP
- [ ] Verify DNS propagation
- [ ] Test HTTPS connection

## 🚀 Deployment Commands

```bash
# On production server
git clone <repository-url>
cd asynchronousdigital
cp .env.example .env
# Edit .env with production values
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm install
npm run build
php artisan storage:link
```

## 🔄 Post-Deployment

- [ ] Test all major features
- [ ] Verify file uploads work
- [ ] Test email sending
- [ ] Check error pages (404, 500)
- [ ] Monitor logs for any issues
- [ ] Set up automated backups
- [ ] Document rollback procedure

## 🔐 Security Notes

**IMPORTANT:** Before deploying to production:
1. Update all sensitive credentials in `.env`
2. Never commit `.env` file to git
3. Use strong passwords for database and admin accounts
4. Keep Telescope disabled in production
5. Regularly update dependencies: `composer update` and `npm update`

## 📝 Environment Variables to Update

Make sure to update these in production `.env`:
- Database credentials (DB_*)
- Mail server settings (MAIL_*)
- APP_URL (production domain)
- Session domain if using subdomain cookies
- Any API keys or third-party service credentials

## 🆘 Rollback Plan

If issues occur:
```bash
git checkout <previous-commit-hash>
composer install --optimize-autoloader --no-dev
php artisan migrate:rollback
php artisan config:clear
php artisan cache:clear
```
