# IT Job Management System - Complete Maintenance & Monitoring Manual

**Last Updated:** January 26, 2026  
**System:** IT Job Management System with Automated Email Reminders  
**Environment:** Live Production Server

---

## Table of Contents

1. [Daily Monitoring Checklist](#daily-monitoring-checklist)
2. [Database Backup & Recovery](#database-backup--recovery)
3. [Deployment & Code Updates](#deployment--code-updates)
4. [Troubleshooting Guide](#troubleshooting-guide)
5. [Emergency Procedures](#emergency-procedures)
6. [Performance Monitoring](#performance-monitoring)
7. [Security & Updates](#security--updates)
8. [Contact & Support](#contact--support)

---

## Daily Monitoring Checklist

### **Morning Check (Every Day)**

Run this command at 09:00 AM to verify all overnight jobs ran:

```bash
cd /var/www/html/it_ticket_system

# 1. Check cron service is running
sudo systemctl is-active cron
# Expected: active (if not, call IT immediately)

# 2. Verify scheduled jobs ran
tail -100 storage/logs/laravel.log | grep -i "reminder\|sent\|confirmation"

# 3. Quick health check
php artisan schedule:list

# 4. Check for errors
tail -50 storage/logs/laravel.log | grep -i "error\|failed"
```

**Expected Results:**
- ✅ Cron: `active`
- ✅ Log contains: `sent`, `reminder`, `confirmation` entries
- ✅ No error messages
- ✅ Next tasks show "22 hours from now"

**If anything failed:**
- See [Troubleshooting Guide](#troubleshooting-guide)

---

### **Weekly Check (Every Friday)**

```bash
cd /var/www/html/it_ticket_system

# 1. Check database size
mysql -u www-data -p -e "SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) as 'Size in MB' FROM information_schema.tables WHERE table_schema = 'it_ticket_system';"

# 2. Count tickets created this week
php artisan tinker --execute="echo \App\Models\Ticket::whereBetween('created_at', [now()->startOfWeek(), now()])->count() . ' tickets created this week';"

# 3. Check error logs
wc -l storage/logs/laravel.log
echo "If above 10000, consider clearing logs"

# 4. Verify backup status
ls -lh /var/backups/ | tail -10
```

---

## Database Backup & Recovery

### **Automatic Daily Backup (Already Configured)**

Backups run automatically every day at 2 AM.

Location: `/var/backups/`

```bash
# View all backups
ls -lh /var/backups/db_*.sql.gz

# Check backup size (should be 1-50 MB typically)
du -h /var/backups/db_*.sql.gz | tail -5
```

---

### **Manual Database Backup**

If you need to backup before doing something risky:

```bash
cd /var/www/html/it_ticket_system

# Get database credentials from .env
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f 2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f 2)
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f 2)

# Create backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > /var/backups/db_manual_$(date +%Y%m%d_%H%M%S).sql.gz

echo "✅ Backup created: /var/backups/db_manual_$(date +%Y%m%d_%H%M%S).sql.gz"
```

---

### **Restore from Backup (IF DATA IS CORRUPTED)**

⚠️ **CRITICAL - Only do this if absolutely necessary!**

```bash
cd /var/www/html/it_ticket_system

# 1. STOP the application
sudo systemctl stop php8.1-fpm  # Stop web server

# 2. List available backups
ls -lh /var/backups/db_*.sql.gz

# 3. Choose backup file and restore
BACKUP_FILE="/var/backups/db_20260126.sql.gz"
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f 2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f 2)
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f 2)

# Drop current database (DANGEROUS!)
mysql -u $DB_USER -p$DB_PASS -e "DROP DATABASE $DB_NAME; CREATE DATABASE $DB_NAME;"

# Restore backup
gunzip < $BACKUP_FILE | mysql -u $DB_USER -p$DB_PASS $DB_NAME

# 4. Restart application
sudo systemctl start php8.1-fpm

echo "✅ Database restored from $BACKUP_FILE"
```

---

## Deployment & Code Updates

### **Production Deployment Checklist**

Follow this EXACTLY when deploying changes:

```bash
cd /var/www/html/it_ticket_system

# ========================================
# STEP 1: BACKUP DATABASE (CRITICAL!)
# ========================================
echo "Step 1: Creating database backup..."
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f 2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f 2)
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f 2)
BACKUP_FILE="/var/backups/pre_deploy_$(date +%Y%m%d_%H%M%S).sql.gz"

mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_FILE
echo "✅ Backup: $BACKUP_FILE"

# ========================================
# STEP 2: UPDATE CODE FROM MAIN BRANCH
# ========================================
echo "Step 2: Updating code from main branch..."
git status
# Should show: "On branch main" or "On branch version"

git checkout main
git pull origin main
echo "✅ Code updated"

# ========================================
# STEP 3: INSTALL DEPENDENCIES
# ========================================
echo "Step 3: Installing dependencies..."
composer install --no-dev --optimize-autoloader
npm ci
npm run build
echo "✅ Dependencies installed"

# ========================================
# STEP 4: RUN MIGRATIONS
# ========================================
echo "Step 4: Running database migrations..."
php artisan migrate --force
echo "✅ Migrations completed"

# ========================================
# STEP 5: CLEAR CACHE
# ========================================
echo "Step 5: Clearing application cache..."
php artisan cache:clear
php artisan config:cache
php artisan route:cache
echo "✅ Cache cleared"

# ========================================
# STEP 6: VERIFY SYSTEM
# ========================================
echo "Step 6: Verifying system..."
php artisan schedule:list | head -5
php artisan tinker --execute="echo 'Database: OK';"
echo "✅ System verified"

# ========================================
# STEP 7: NOTIFY
# ========================================
echo ""
echo "✅ ==============================================="
echo "✅ DEPLOYMENT SUCCESSFUL!"
echo "✅ Backup: $BACKUP_FILE"
echo "✅ Application is live"
echo "✅ ==============================================="
```

Save this as `/var/www/html/it_ticket_system/deploy.sh`:

```bash
chmod +x deploy.sh
./deploy.sh
```

---

### **Switching Branches (For Testing)**

```bash
# Development branch (for testing new features)
git checkout version
git pull origin version

# Production branch (stable)
git checkout main
git pull origin main
```

⚠️ **Important:** No database issues when switching - safe to do anytime!

---

## Troubleshooting Guide

### **Problem 1: Scheduled Jobs Not Running**

**Symptoms:**
- No emails sent
- `schedule:list` shows tasks but they're not executing
- Logs show no recent activity

**Solution:**

```bash
cd /var/www/html/it_ticket_system

# 1. Check cron service
sudo systemctl status cron

# If NOT running:
sudo systemctl start cron
sudo systemctl enable cron

# 2. Verify cron job is installed
sudo crontab -u www-data -l

# Should show:
# * * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1

# If missing, add it:
sudo crontab -u www-data -e
# Add this line:
# * * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1

# 3. Check system cron logs
sudo journalctl -u cron --since "2 hours ago" | tail -20
```

---

### **Problem 2: Emails Not Sending**

**Symptoms:**
- Reminders not going to mailboxes
- No errors in Laravel logs
- Command says "sent" but email never arrives

**Solution:**

```bash
cd /var/www/html/it_ticket_system

# 1. Check mail configuration
grep MAIL .env
# Check: MAIL_HOST, MAIL_PORT, MAIL_USERNAME

# 2. Test mail manually
php artisan tinker
Mail::raw('Test email', function($msg) {
    $msg->to('your-email@example.com')
        ->subject('Test');
});
# Should return: true

# 3. Check Laravel logs for mail errors
tail -100 storage/logs/laravel.log | grep -i "mail\|smtp"

# 4. If SMTP error, check mail server
telnet mail.example.com 587
# Should connect without error

# 5. Clear queue (if using queue)
php artisan queue:flush
```

---

### **Problem 3: Database Connection Error**

**Symptoms:**
```
SQLSTATE[HY000]: General error: 2006 MySQL server has gone away
```

**Solution:**

```bash
# 1. Check MySQL is running
sudo systemctl status mysql
# or
sudo systemctl status mariadb

# 2. If not running, start it
sudo systemctl start mysql

# 3. Verify database exists
mysql -u www-data -p -e "SHOW DATABASES;"

# 4. Check .env has correct credentials
cat .env | grep DB_

# 5. Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

---

### **Problem 4: High Memory Usage**

**Symptoms:**
- Website slow
- Server unresponsive
- `free -h` shows little memory available

**Solution:**

```bash
# 1. Check memory usage
free -h
top -b -n 1 | head -20

# 2. Find memory hogs
ps aux --sort=-%mem | head -10

# 3. Clear Laravel cache
php artisan cache:clear
php artisan config:cache

# 4. Clear old logs
php artisan tinker --execute="DB::delete('DELETE FROM users WHERE created_at < now() - interval 6 month');"

# 5. Optimize database
mysql -u www-data -p -e "OPTIMIZE TABLE it_ticket_system.*;"
```

---

## Emergency Procedures

### **Website Down (500 Error)**

```bash
cd /var/www/html/it_ticket_system

# 1. Check error log
tail -50 storage/logs/laravel.log

# 2. Check if files have permission issues
sudo chown -R www-data:www-data /var/www/html/it_ticket_system
sudo chmod -R 755 /var/www/html/it_ticket_system/app
sudo chmod -R 755 /var/www/html/it_ticket_system/bootstrap

# 3. Clear cache
php artisan cache:clear
php artisan config:cache

# 4. Restart web server
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm

# 5. Test
curl http://localhost
```

---

### **Database Corrupt/Lost Data**

```bash
# IMMEDIATE ACTIONS:
# 1. Stop application
sudo systemctl stop php8.1-fpm

# 2. Find latest backup
ls -lhrt /var/backups/db_*.sql.gz | tail -1

# 3. Restore (see Database Backup & Recovery section)

# 4. Verify
php artisan tinker --execute="echo \App\Models\Ticket::count() . ' tickets';"

# 5. Restart
sudo systemctl start php8.1-fpm

# Report this incident!
```

---

### **Hacked / Security Breach**

```bash
# 1. IMMEDIATELY change all passwords
php artisan tinker
\App\Models\User::all()->each(function($user) {
    $user->password = bcrypt('new-secure-password');
    $user->save();
});

# 2. Check for suspicious files
find /var/www/html/it_ticket_system -type f -newer /var/backups/db_*.sql.gz

# 3. Review recent commits
git log --oneline -20

# 4. If files modified, rollback
git checkout main

# 5. Restore database from backup
# (See Database Backup & Recovery)

# 6. Update all credentials
# Update .env file

# 7. Run security check
php artisan tinker --execute="echo 'Security check required';"

# Contact your IT Security team!
```

---

## Performance Monitoring

### **Daily Performance Report**

Create `/var/www/html/it_ticket_system/monitor_daily.sh`:

```bash
#!/bin/bash

echo "========================================="
echo "DAILY PERFORMANCE REPORT - $(date)"
echo "========================================="

# System Load
echo ""
echo "--- SYSTEM LOAD ---"
uptime

# Disk Space
echo ""
echo "--- DISK SPACE ---"
df -h /var/www/html/it_ticket_system

# Database Size
echo ""
echo "--- DATABASE SIZE ---"
mysql -u www-data -p -e "SELECT 
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as 'Size MB'
    FROM information_schema.tables 
    WHERE table_schema = 'it_ticket_system';"

# Active Connections
echo ""
echo "--- ACTIVE USERS ---"
ps aux | grep -c php-fpm

# Error Count
echo ""
echo "--- ERRORS TODAY ---"
grep -c "ERROR\|error\|failed" storage/logs/laravel.log

# Backup Status
echo ""
echo "--- LATEST BACKUP ---"
ls -lh /var/backups/db_*.sql.gz | tail -1

echo ""
echo "========================================="
```

Run it:
```bash
chmod +x monitor_daily.sh
./monitor_daily.sh > storage/logs/daily_report_$(date +%Y%m%d).txt
```

---

### **Monitor Key Metrics**

```bash
# CPU Usage (should be < 80%)
top -bn1 | grep "Cpu(s)" | awk '{print "CPU Usage: " $2}'

# Memory Usage (should be < 80%)
free -h | awk 'NR==2 {print "Memory Usage: " ($3/$2) * 100 "%"}'

# Disk Usage (should be < 80%)
df -h /var/www/html/it_ticket_system | awk 'NR==2 {print "Disk Usage: " ($3/$2) * 100 "%"}'

# Database Connections
mysql -u www-data -p -e "SHOW PROCESSLIST;"

# Website Response Time
curl -w "Response time: %{time_total}s\n" -o /dev/null -s http://localhost
```

---

## Security & Updates

### **Monthly Security Checklist**

```bash
cd /var/www/html/it_ticket_system

# 1. Update OS packages
sudo apt update
sudo apt upgrade -y

# 2. Update PHP dependencies
composer update --no-dev

# 3. Update Node dependencies
npm update

# 4. Update Laravel
composer update laravel/framework

# 5. Security audit
composer audit

# 6. Test everything
php artisan schedule:list
curl http://localhost

# 7. Commit changes
git add composer.lock package-lock.json
git commit -m "Monthly security updates - $(date +%Y-%m)"
git push origin main
```

---

### **Change Passwords Quarterly**

```bash
# Every 3 months, change database password

# 1. Create new password
NEW_PASS="GenerateSecurePassword123!"

# 2. Update database user
mysql -u root -p -e "ALTER USER 'www-data'@'localhost' IDENTIFIED BY '$NEW_PASS';"

# 3. Update .env
nano .env
# Change: DB_PASSWORD=NewPassword123!

# 4. Clear cache
php artisan cache:clear

# 5. Test
php artisan tinker --execute="echo 'Database OK';"
```

---

## Contact & Support

### **When to Contact IT**

Contact your IT department if:
- ❌ Cron service is not running
- ❌ Database is corrupted
- ❌ Website shows 500 error for > 1 hour
- ❌ Emails not sending for entire day
- ❌ Suspicious files or unauthorized access
- ❌ Unable to restore from backup
- ❌ Server out of disk space

### **Emergency Contact**
- **During Business Hours:** Call IT Help Desk
- **After Hours:** Use emergency contact list (see posted notice)

### **Escalation**
If basic troubleshooting fails:
1. Take screenshot of error
2. Export logs: `tar -czf logs_$(date +%Y%m%d).tar.gz storage/logs/`
3. Email to IT with:
   - Error description
   - Steps taken
   - Screenshots/logs

---

## Scheduled Maintenance

### **Every Night (Automated)**

✅ **01:00 AM** - Database backup
✅ **08:00-08:30 AM** - All scheduled email reminders
✅ **02:00 AM** - Log rotation

### **Every Week (Manual)**

**Every Friday:**
- Review performance report
- Check error logs
- Verify backup completion

### **Every Month (Manual)**

- Update packages and dependencies
- Review security audit
- Check disk space trends
- Review user activity

### **Every Quarter (Manual)**

- Update database password
- Full security review
- Performance optimization
- Clean up old logs

---

## Quick Command Reference

```bash
# Restart web server
sudo systemctl restart nginx php8.1-fpm

# Clear all cache
php artisan cache:clear && php artisan config:cache

# Check scheduled jobs
php artisan schedule:list

# View logs
tail -100 storage/logs/laravel.log

# Database backup
mysqldump -u www-data -pPASS it_ticket_system | gzip > backup.sql.gz

# Deploy new code
git checkout main && git pull && composer install && php artisan migrate --force

# Emergency: Stop all emails
php artisan queue:flush

# Emergency: Restart PHP
sudo systemctl restart php8.1-fpm

# Check system status
top
free -h
df -h
```

---

**Remember: When in doubt, backup first, then investigate!** 🎯

Last reviewed: January 26, 2026
