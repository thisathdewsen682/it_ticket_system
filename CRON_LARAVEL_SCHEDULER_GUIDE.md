# Cron & Laravel Scheduler - Complete Learning Guide

## How It Works: The Complete Flow

```
┌─────────────────────────────────────────────────────────────┐
│  System Clock                                               │
│  Every 1 minute → checks if something needs to run          │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  CRON (Linux/Unix Scheduler)                                │
│  Reads crontab file & triggers scheduled commands           │
│  Current Job:                                               │
│  * * * * * cd /var/www/html/it_ticket_system &&            │
│            php artisan schedule:run >> /dev/null 2>&1       │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  Laravel Scheduler (schedule:run)                           │
│  Reads routes/console.php and checks:                       │
│  "Are any tasks due RIGHT NOW?"                             │
└──────────────────────┬──────────────────────────────────────┘
                       │
        ┌──────────────┼──────────────┐
        │              │              │
        ▼              ▼              ▼
   08:00 due?    08:02 due?      08:04 due?
   YES ↓         YES ↓           YES ↓
   Run cmd1      Run cmd2        Run cmd3
```

---

## Component 1: CRON (The Foundation)

### What is Cron?
- **Linux/Unix system service** that runs scheduled tasks
- **Runs every minute** by checking the crontab file
- **Executes commands** at specified times

### The Crontab File
```bash
# Format: minute hour day month day-of-week command
* * * * * /path/to/command
│ │ │ │ │
│ │ │ │ └─ Day of week (0-6, Sunday=0)
│ │ │ └─── Month (1-12)
│ │ └───── Day of month (1-31)
│ └─────── Hour (0-23)
└───────── Minute (0-59)
```

### Our Cron Job Breakdown
```bash
* * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1
│ │ │ │ │
└─┴─┴─┴─┴─ "EVERY minute" (all wildcards = every time)
          └─ "cd to project, run scheduler, hide output"
```

**Meaning:** "Run Laravel scheduler every single minute, 24/7"

### Check Your Cron Job
```bash
# View crontab for www-data user
sudo crontab -u www-data -l

# Edit crontab
sudo crontab -u www-data -e

# Remove all cron jobs
sudo crontab -u www-data -r

# View cron logs
sudo grep CRON /var/log/syslog | tail -20
```

---

## Component 2: Laravel Scheduler

### What is Schedule:run?
- **Laravel command** that checks if tasks are due
- **Reads routes/console.php** for task definitions
- **Executes only tasks that are due RIGHT NOW**

### Schedule Definition (routes/console.php)
```php
Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('08:02')              // Run at 08:02 AM daily
    ->withoutOverlapping()           // Don't run multiple times
    ->onOneServer();                 // Single server only
```

### How Laravel Scheduler Decides to Run

**When cron executes `php artisan schedule:run`:**

1. **Reads all tasks** from routes/console.php
2. **For each task**, checks:
   - Is the scheduled time EXACTLY now? (within 1-minute window)
   - Has it already run? (checks `storage/framework/schedule-*` files)
   - Is it due on this day/time?
3. **Runs only matching tasks**
4. **Records execution** in `storage/framework/` directory

### Your Scheduled Tasks
```bash
# View all configured tasks
php artisan schedule:list

# Example output:
#  0  8 * * *  php artisan tickets:send-unassigned-reminder
#  2  8 * * *  php artisan tickets:send-approval-reminders
#  4  8 * * *  php artisan tickets:send-assigned-reminder
#  etc...
```

---

## How They Work Together

### Timeline Example (08:02 AM)

```
08:01:59
  └─ Cron: Nothing scheduled, wait...

08:02:00 ← THIS EXACT SECOND
  ├─ Cron: Check crontab... schedule:run is due! RUN IT!
  │
  └─► Laravel Scheduler (schedule:run):
       ├─ Read routes/console.php
       ├─ Check: Is any task due at 08:02?
       │  └─ YES! 'tickets:send-approval-reminders'
       ├─ Execute: php artisan tickets:send-approval-reminders
       │  └─ Query database for pending tickets
       │  └─ Send emails to approvers
       │  └─ Record status
       └─ Exit

08:02:01
  └─ Cron: Done, wait 1 minute...

08:03:00
  ├─ Cron: Check crontab... schedule:run is due! RUN IT!
  │
  └─► Laravel Scheduler (schedule:run):
       ├─ Check: Is any task due at 08:03?
       │  └─ NO, none scheduled for this minute
       └─ Exit silently

08:04:00 ← NEXT TASK
  ├─ Cron: Check crontab... schedule:run is due! RUN IT!
  │
  └─► Laravel Scheduler (schedule:run):
       ├─ Check: Is any task due at 08:04?
       │  └─ YES! 'tickets:send-assigned-reminder'
       ├─ Execute command...
       └─ Exit
```

---

## Troubleshooting Guide

### Issue 1: Cron Job Not Running

**Symptom:** "Emails are not being sent at scheduled times"

**Check 1: Is cron service running?**
```bash
sudo systemctl status cron
# Should show: Active: active (running)
```

**Check 2: Is cron job installed?**
```bash
sudo crontab -u www-data -l
# Should show your schedule:run job
```

**Check 3: View cron execution logs**
```bash
# Ubuntu/Debian
sudo grep CRON /var/log/syslog | tail -20

# RedHat/CentOS
sudo tail -f /var/log/cron
```

**Fix: Reinstall cron job**
```bash
sudo crontab -u www-data -e
# Add this line:
* * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1
# Save (Ctrl+X, then Y, then Enter)
```

---

### Issue 2: Tasks Not Executing

**Symptom:** "Cron is running, but tasks don't execute"

**Check 1: Verify scheduled tasks**
```bash
cd /var/www/html/it_ticket_system
php artisan schedule:list
# Look for "Next Due" times
```

**Check 2: Test command manually**
```bash
# Run a single reminder command manually
php artisan tickets:send-approval-reminders

# If it works manually but not via cron:
# → Problem is in environment/permissions, not the command
```

**Check 3: Check Laravel logs**
```bash
# View recent errors
tail -50 /var/www/html/it_ticket_system/storage/logs/laravel.log

# Search for schedule errors
grep -i "schedule\|reminder\|mail" storage/logs/laravel.log | tail -20
```

**Check 4: Is PHP executable from cron?**
```bash
# Test if PHP is available in cron environment
sudo crontab -u www-data -e

# Add test line:
* * * * * /usr/bin/php -v >> /var/www/html/it_ticket_system/php-test.log 2>&1

# Wait 1 minute, then check:
cat /var/www/html/it_ticket_system/php-test.log

# If no output → PHP path is wrong
# Find correct PHP path:
which php
# or
whereis php
```

---

### Issue 3: Emails Not Being Sent

**Symptom:** "Command runs, but no emails arrive"

**Check 1: Is mail configuration correct?**
```bash
# Check .env file
cat .env | grep MAIL_

# Should show (example):
# MAIL_DRIVER=smtp
# MAIL_HOST=smtp.mailtrap.io
# MAIL_FROM_ADDRESS=noreply@example.com
```

**Check 2: Test mail directly**
```bash
php artisan tinker
>>> \Illuminate\Support\Facades\Mail::raw('Test email', function($m) { $m->to('your@email.com')->subject('Test'); });
>>> exit
```

**Check 3: Check mail logs**
```bash
# If using file driver:
cat storage/logs/mail.log

# If using SMTP:
tail -50 storage/logs/laravel.log | grep -i mail
```

---

### Issue 4: Cron Running Too Often

**Symptom:** "Emails being sent multiple times"

**Why:** `withoutOverlapping()` is not working properly

**Fix:**
```bash
# Clear Laravel cache (locks from overlapping prevention)
php artisan cache:clear

# Check filesystem permissions
ls -la storage/framework/cache/
# Should be writable by www-data user
```

---

## Monitoring & Maintenance

### Check Current Status
```bash
# 1. Is cron service running?
sudo systemctl status cron

# 2. Are all tasks configured?
php artisan schedule:list

# 3. When will next task run?
# Look at "Next Due" column

# 4. Check recent execution logs
sudo tail -30 /var/log/syslog | grep CRON
```

### Enable Detailed Logging

**Edit routes/console.php:**
```php
Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('08:02')
    ->withoutOverlapping()
    ->onOneServer()
    ->after(function () {
        \Log::info('Approval reminders completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Approval reminders failed!');
    });
```

**View logs:**
```bash
tail -f storage/logs/laravel.log
```

---

### Test Schedule Manually

```bash
# Run scheduler in verbose mode (see what happens)
php artisan schedule:run --verbose

# Output example:
# Running scheduled command: tickets:send-approval-reminders
# Reminder sent to manager@example.com
# Summary: 1 reminder(s) sent.
```

---

## Common Issues Quick Reference

| Issue | Cause | Solution |
|-------|-------|----------|
| Emails not sent at scheduled time | Cron not installed | `sudo crontab -u www-data -e` (add job) |
| Tasks running multiple times | `withoutOverlapping()` broken | `php artisan cache:clear` |
| "Unknown column" errors | Database connection | Check `.env` DB credentials |
| Tasks listed but not running | Wrong time configuration | Check system timezone: `php artisan schedule:list` |
| PHP not found in logs | PHP path incorrect in cron | Use full path: `/usr/bin/php` not just `php` |
| Permission denied errors | www-data can't access files | `sudo chown -R www-data:www-data /var/www/html/it_ticket_system` |
| Tasks run but don't send mail | MAIL_DRIVER misconfigured | Verify `.env` mail settings |

---

## Environment Variables That Matter

```bash
# In your .env file, these affect scheduling:

# Timezone for all scheduled times
APP_TIMEZONE=Asia/Colombo

# Mail delivery configuration
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="IT Ticket System"

# Logging
LOG_LEVEL=debug  # Set to 'debug' to see more details
```

---

## Testing Workflow

**Before going to production, test this:**

```bash
# 1. Verify cron job is installed
sudo crontab -u www-data -l

# 2. List all scheduled tasks
php artisan schedule:list

# 3. Run each command manually
php artisan tickets:send-approval-reminders
php artisan tickets:send-assigned-reminder
php artisan tickets:send-it-manager-reminders
# ... etc for all commands

# 4. Check logs for errors
tail -50 storage/logs/laravel.log

# 5. Verify emails were queued
# Check your mail service (Mailtrap, SMTP server, etc.)

# 6. Wait for next scheduled time and verify automatic execution
# Check logs again at next scheduled time
```

---

## Quick Debugging Commands

```bash
# See what cron will execute
sudo crontab -u www-data -l

# Test if command works
cd /var/www/html/it_ticket_system && php artisan schedule:run --verbose

# Check for errors
grep -i error storage/logs/laravel.log | tail -10

# See all recent cron executions
sudo grep CRON /var/log/syslog | tail -20

# Clear caches that might block execution
php artisan cache:clear

# Check filesystem permissions
ls -la storage/framework/schedule-*

# Verify www-data can write logs
ls -la storage/logs/

# Check current system time (must match scheduled time)
date
```

---

## Key Takeaways

1. **Cron** = Linux scheduler that runs a command EVERY MINUTE
2. **Laravel Scheduler** = Checks if any tasks are due and runs them
3. **Your Setup** = Every minute, cron runs `schedule:run` which checks all tasks
4. **Staggered Times** = Tasks run at different minutes (08:00, 08:02, 08:04, etc.) to avoid overload
5. **Failures** = Check logs in `storage/logs/laravel.log`
6. **Testing** = Always run commands manually first, then let cron handle it

---

**You're now ready to troubleshoot! 🚀**
