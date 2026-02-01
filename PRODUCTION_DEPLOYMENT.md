# Production Deployment Guide

## 🚨 CRITICAL: Queue Worker Required for Emails

**Emails will NOT send without a running queue worker!** This is the most important step.

## Pre-Deployment Checklist

- [ ] Backup existing database
- [ ] Review `.env.prod` settings
- [ ] Test in staging environment
- [ ] Build production assets: `npm run build`
- [ ] Commit all changes to main branch

## Manual Steps Required on Production Server

### 1. Pull Latest Code
```bash
cd /var/www/asynchronousdigital
git pull origin main
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install --production
npm run build
```

### 3. Configure Environment
Copy `.env.prod` to `.env` or update existing `.env`:
```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=database  # CRITICAL for emails!
MAIL_MAILER=smtp
TELESCOPE_ENABLED=false
```

### 4. Run Migrations
```bash
php artisan migrate --force
```

### 5. Seed Database (First Time Only)
```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminUserSeeder
```

### 6. Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 7. Set File Permissions
```bash
sudo chown -R www-data:www-data /var/www/asynchronousdigital
sudo chmod -R 775 /var/www/asynchronousdigital/storage
sudo chmod -R 775 /var/www/asynchronousdigital/bootstrap/cache
```

### 8. 🔴 SETUP QUEUE WORKER (REQUIRED!)

**Without this step, invitation emails will queue but never send!**

#### Option A: Using Supervisor (Recommended)

1. Install supervisor:
```bash
sudo apt-get install supervisor
```

2. Create config: `/etc/supervisor/conf.d/asynchronousdigital-worker.conf`
```ini
[program:asynchronousdigital-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/asynchronousdigital/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasup=unexpected
numprocs=2
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/asynchronousdigital/storage/logs/worker.log
stopwaitsecs=3600
```

3. Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start asynchronousdigital-worker:*
```

4. Verify it's running:
```bash
sudo supervisorctl status asynchronousdigital-worker:*
```

#### Option B: Using Systemd

1. Create service: `/etc/systemd/system/asynchronousdigital-queue.service`
```ini
[Unit]
Description=Asynchronous Digital Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/asynchronousdigital
ExecStart=/usr/bin/php /var/www/asynchronousdigital/artisan queue:work database --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

2. Enable and start:
```bash
sudo systemctl enable asynchronousdigital-queue
sudo systemctl start asynchronousdigital-queue
sudo systemctl status asynchronousdigital-queue
```

### 9. Setup Scheduled Tasks (Optional)
```bash
crontab -e
```
Add:
```
* * * * * cd /var/www/asynchronousdigital && php artisan schedule:run >> /dev/null 2>&1
```

### 10. Restart Web Server
```bash
sudo service nginx restart  # or apache2
sudo service php8.3-fpm restart
```

## Post-Deployment Verification

Test these in order:

1. **Website loads**: https://asynchronousdigital.com ✅
2. **Admin login works**: admin@asynchronousdigital.com / password ✅
3. **Queue worker running**:
   ```bash
   sudo supervisorctl status asynchronousdigital-worker:*
   # Should show RUNNING
   ```
4. **Create test user with email checkbox** - Email should send within seconds ✅
5. **Check logs**:
   ```bash
   tail -f storage/logs/laravel.log
   tail -f storage/logs/worker.log
   ```
6. **Verify failed jobs** (should be empty):
   ```bash
   php artisan queue:failed
   ```

## Gmail SMTP Configuration

1. Go to https://myaccount.google.com/security
2. Enable 2-Step Verification
3. Generate App Password: https://myaccount.google.com/apppasswords
4. Add to `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=asynchronousd@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="asynchronousd@gmail.com"
MAIL_FROM_NAME="Asynchronous Digital"
```

## Troubleshooting

### Emails Not Sending

1. **Check queue worker status**:
```bash
sudo supervisorctl status asynchronousdigital-worker:*
```
Should show "RUNNING". If not, start it.

2. **Check for failed jobs**:
```bash
php artisan queue:failed
```
If any failed, check error and retry:
```bash
php artisan queue:retry all
```

3. **Check worker logs**:
```bash
tail -f storage/logs/worker.log
```

4. **Verify SMTP credentials**:
```bash
php artisan tinker
Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
exit
```

5. **Restart queue worker**:
```bash
sudo supervisorctl restart asynchronousdigital-worker:*
```

### Permission Errors
```bash
sudo chown -R www-data:www-data /var/www/asynchronousdigital
sudo chmod -R 775 /var/www/asynchronousdigital/storage
```

### Clear All Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## Monitoring Commands

```bash
# Watch queue in real-time
watch -n 1 'php artisan queue:monitor'

# View recent logs
tail -f storage/logs/laravel.log

# View worker logs
tail -f storage/logs/worker.log

# Check queue worker status
sudo supervisorctl status

# Restart queue worker
sudo supervisorctl restart asynchronousdigital-worker:*

# View failed jobs
php artisan queue:failed
```

## Important Production Notes

1. **Queue Worker**: MUST be running or emails won't send
2. **Gmail App Password**: Regular password won't work
3. **Change Admin Password**: Immediately after deployment
4. **Email Environment Prefix**: Production emails have no [ENV] prefix
5. **File Storage**: Uses Digital Ocean Spaces (pre-configured)
6. **Telescope**: Disabled in production for security
7. **Session Encryption**: Enabled for security
8. **Daily Logs**: Log files rotate daily

## Rollback Procedure

If something goes wrong:

```bash
# 1. Revert code
cd /var/www/asynchronousdigital
git reset --hard HEAD~1

# 2. Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Restart services
sudo service php8.3-fpm restart
sudo service nginx restart
sudo supervisorctl restart asynchronousdigital-worker:*
```

## Regular Maintenance

### Weekly
- Check failed jobs: `php artisan queue:failed`
- Review logs: `tail -100 storage/logs/laravel.log`
- Monitor disk space: `df -h`

### Monthly
- Rotate old logs: Delete logs older than 30 days
- Database backup verification
- Update dependencies: `composer update --no-dev`

## Support

- Logs: `/var/www/asynchronousdigital/storage/logs/`
- Email: support@asynchronousdigital.com
- Documentation: [USER_INVITATION_SYSTEM.md](USER_INVITATION_SYSTEM.md)
