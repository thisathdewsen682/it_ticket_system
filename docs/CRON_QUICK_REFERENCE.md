# Quick Reference: Cron & Scheduler Cheat Sheet

## Current Status: ✅ ALL OPERATIONAL

```
Cron Service:      ACTIVE (running)
Cron Job:          INSTALLED ✓
Laravel Scheduler: CONFIGURED ✓
Current Time:      09:34 AM
System Timezone:   Asia/Colombo
```

---

## How to Monitor (Daily Checks)

### 1. Quick Health Check
```bash
# See if everything is working
sudo systemctl is-active cron          # Should say: active
sudo crontab -u www-data -l            # Should show schedule:run
date                                    # Check current time
```

### 2. Check Scheduled Tasks
```bash
cd /var/www/html/it_ticket_system
php artisan schedule:list
```

**Output shows:**
- Task name
- Cron time (0 8 * * * = 08:00 AM daily)
- Next execution time

### 3. Check Logs After Task Runs
```bash
# View Laravel logs
tail -50 storage/logs/laravel.log

# View cron execution logs
sudo grep CRON /var/log/syslog | tail -10

# Check for mail-specific issues
tail -20 storage/logs/laravel.log | grep -i mail
```

---

## Troubleshooting Flowchart

```
Problem: "Reminders not being sent"
         │
         └─► Is Cron Service Running?
             sudo systemctl status cron
             │
             ├─ NO → Start it: sudo systemctl start cron
             │
             └─ YES ↓
                  │
                  └─► Is Cron Job Installed?
                      sudo crontab -u www-data -l
                      │
                      ├─ NO → Install it (see below)
                      │
                      └─ YES ↓
                           │
                           └─► Does Task Run Manually?
                               php artisan tickets:send-approval-reminders
                               │
                               ├─ YES → Problem is timing/system
                               │   └─ Check: date (system time correct?)
                               │   └─ Check: logs (errors?)
                               │
                               └─ NO → Problem is command/config
                                   └─ Check: .env file
                                   └─ Check: database connection
                                   └─ Check: mail configuration
```

---

## Emergency Commands

### Install Cron Job (If Missing)
```bash
sudo crontab -u www-data -e

# Then add this line:
* * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1

# Save: Ctrl+X, then Y, then Enter
```

### Start Cron Service (If Stopped)
```bash
sudo systemctl start cron
sudo systemctl enable cron    # Auto-start on reboot
```

### Clear Locks (If Tasks Won't Run)
```bash
php artisan cache:clear
```

### Force Run All Tasks (Testing)
```bash
# Run scheduler in verbose mode
php artisan schedule:run --verbose

# Run specific task
php artisan tickets:send-approval-reminders
```

### View All Errors
```bash
grep -i "error\|failed\|exception" storage/logs/laravel.log | tail -20
```

---

## The Flow (Step by Step)

### What Happens at 08:02 AM:

```
08:02:00 AM
    ↓
System Clock triggers
    ↓
Cron reads crontab file
    ↓
Cron finds: * * * * * cd /var... && php artisan schedule:run
    ↓
Cron executes: php artisan schedule:run
    ↓
Laravel Scheduler starts
    ↓
Reads routes/console.php
    ↓
Checks: "Is anything due at 08:02?"
    ↓
YES! tickets:send-approval-reminders
    ↓
Executes: php artisan tickets:send-approval-reminders
    ↓
Command queries database
    ↓
Finds pending tickets with approval_user_id set
    ↓
Sends emails to each approver
    ↓
Logs result in storage/logs/laravel.log
    ↓
Scheduler exits
    ↓
08:02:30 AM - Process complete
```

---

## Key Files & Locations

| File | Purpose | What to Check |
|------|---------|---------------|
| `/var/spool/cron/crontabs/www-data` | Cron job storage | `sudo crontab -u www-data -l` |
| `routes/console.php` | Task definitions | Times, commands, conditions |
| `storage/logs/laravel.log` | Laravel logs | Errors, execution details |
| `.env` | Configuration | MAIL settings, timezone |
| `app/Console/Commands/` | Command files | Business logic for each task |
| `app/Mail/` | Email templates | Mail class definitions |
| `/var/log/syslog` | System logs | Cron execution records |

---

## Verification Checklist

Before considering the system "production ready":

- [ ] Cron service is running: `sudo systemctl is-active cron`
- [ ] Cron job is installed: `sudo crontab -u www-data -l`
- [ ] All tasks listed: `php artisan schedule:list`
- [ ] Each command works manually: `php artisan tickets:send-approval-reminders`
- [ ] .env has correct MAIL settings
- [ ] System timezone is correct: `date`
- [ ] Logs directory is writable: `ls -la storage/logs/`
- [ ] Tested at actual scheduled time (waited for task to run automatically)

---

## Common Time Issues

### If Tasks Run at Wrong Time

**Check 1: System Timezone**
```bash
date
# Shows current timezone

# If wrong, change in .env:
APP_TIMEZONE=Asia/Colombo  # Update your timezone
```

**Check 2: Server Time Drift**
```bash
# Install NTP (Network Time Protocol) to sync time
sudo apt-get install ntp
sudo systemctl restart ntp
```

### If Tasks Run Multiple Times

**Fix: Clear task locks**
```bash
php artisan cache:clear
rm -f storage/framework/schedule-*
```

---

## Real-World Examples

### Example 1: Check if reminders were sent today

```bash
# Check logs for approval reminder execution
grep "tickets:send-approval-reminders\|Checking for pending approval" storage/logs/laravel.log | tail -5

# Output example:
# Checking for pending approval tickets...
# Ticket #5: Reminder sent to manager@example.com
# Summary: 1 reminder(s) sent.
```

### Example 2: Debug why task didn't run

```bash
# Check if task was scheduled to run
grep "08:02" storage/logs/laravel.log | grep -i approval

# Check cron logs for execution
sudo grep schedule:run /var/log/syslog | grep "Jan 26 08:0"

# Check for errors
grep -A5 "tickets:send-approval-reminders" storage/logs/laravel.log | grep -i error
```

### Example 3: Test before going live

```bash
# 1. Run all commands manually
php artisan tickets:send-unassigned-reminder
php artisan tickets:send-approval-reminders
php artisan tickets:send-assigned-reminder
php artisan tickets:send-it-manager-reminders

# 2. Check logs for any issues
tail -100 storage/logs/laravel.log

# 3. Wait for actual scheduled time (e.g., 08:02 AM)

# 4. Check logs again to confirm automatic execution
tail -20 storage/logs/laravel.log
```

---

## Support Matrix

| Issue | Quick Fix | Full Docs |
|-------|-----------|-----------|
| Cron not running | `sudo systemctl start cron` | CRON_LARAVEL_SCHEDULER_GUIDE.md |
| Tasks not executing | `php artisan cache:clear` | Troubleshooting section |
| Emails not sending | Check `.env` MAIL_* settings | Issue 3 in guide |
| Duplicate emails | `php artisan cache:clear` | Issue 4 in guide |
| Wrong time | `APP_TIMEZONE` in `.env` | Common Time Issues |

---

## Testing Workflow (Recommended)

```bash
# Week 1: Manual Testing
Day 1:  php artisan tickets:send-approval-reminders
Day 2:  php artisan tickets:send-assigned-reminder
Day 3:  php artisan tickets:send-it-manager-reminders
        Check logs after each

# Week 2: Automatic Testing
Day 1-6: Monitor logs at scheduled times
         Verify emails are sent automatically
         No manual intervention needed

# Week 3: Production
        Deploy to production
        Monitor for 1 week
        Be ready with troubleshooting commands
```

---

**Last Updated:** January 26, 2026
**Status:** ✅ Production Ready
**Support:** See CRON_LARAVEL_SCHEDULER_GUIDE.md for detailed help
