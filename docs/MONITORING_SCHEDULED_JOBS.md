# Monitoring Scheduled Jobs - Complete Guide

## 1. CHECK SCHEDULED TASKS (What's configured to run)

### Command:
```bash
php artisan schedule:list
```

### Output Shows:
- All scheduled commands
- When they're scheduled to run (cron format)
- Next due date/time
- How many minutes until next execution

### Example:
```
  0  8  * * *  php artisan tickets:send-unassigned-reminder ............ Next Due: 22 hours from now
  1  8  * * *  php artisan tickets:send-unassigned-deadline-warning ... Next Due: 22 hours from now
```

---

## 2. VERIFY CRON SERVICE IS RUNNING

### Command:
```bash
sudo systemctl status cron
```

### Expected Output:
```
     Active: active (running) since Tue 2026-01-20 18:18:47 +0530; 5 days ago
```

### If NOT Running:
```bash
sudo systemctl start cron
sudo systemctl enable cron  # Auto-start on reboot
```

---

## 3. CHECK INSTALLED CRON JOB

### Command:
```bash
sudo crontab -u www-data -l
```

### Expected Output:
```
* * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1
```

**What this means:**
- `* * * * *` = Run every minute at the :00 second mark
- `php artisan schedule:run` = Laravel's scheduler (checks all tasks)
- `>> /dev/null 2>&1` = Suppress output (silent execution)

### If NOT Found:
```bash
sudo crontab -u www-data -e
# Add this line:
* * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1
```

---

## 4. VIEW EXECUTION LOGS

### Real-time Log Monitoring (last 30 lines):
```bash
tail -30 storage/logs/laravel.log
```

### Watch Live Logs (updates as they happen):
```bash
tail -f storage/logs/laravel.log
```

### Search for Specific Command Logs:
```bash
tail -100 storage/logs/laravel.log | grep "approval-confirmation"
tail -100 storage/logs/laravel.log | grep "sent"
tail -100 storage/logs/laravel.log | grep -i "error"
```

### Count Messages from Today:
```bash
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log | wc -l
```

---

## 5. RUN COMMANDS MANUALLY (For Testing)

### Test a Specific Command:
```bash
php artisan tickets:send-approval-confirmation-reminders
```

### Output Example:
```
Checking for tickets pending approver confirmation...
Approver confirmation reminder sent to Thisath Dewsen for 1 ticket(s)
Summary: 1 approver confirmation reminder(s) sent.
```

### Run All Scheduled Jobs Right Now:
```bash
php artisan schedule:run
```

---

## 6. CHECK CRON EXECUTION (System Level)

### View System Cron Log:
```bash
grep CRON /var/log/syslog | tail -20
```

### Example Output:
```
Jan 26 10:15:01 ubuntu CRON[1234]: (www-data) CMD (cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1)
```

### For Ubuntu 22+ (using journalctl):
```bash
sudo journalctl -u cron --since "2 hours ago" | tail -20
```

---

## 7. MONITORING CHECKLIST

Use this daily/weekly checklist:

```bash
#!/bin/bash
echo "=== SCHEDULED JOBS MONITORING ==="
echo ""

echo "1. Cron Service Status:"
sudo systemctl is-active cron && echo "✅ RUNNING" || echo "❌ STOPPED"
echo ""

echo "2. Cron Job Installed:"
sudo crontab -u www-data -l | grep "schedule:run" && echo "✅ FOUND" || echo "❌ MISSING"
echo ""

echo "3. Recent Errors:"
tail -20 storage/logs/laravel.log | grep -i "error" | wc -l
echo ""

echo "4. Next Scheduled Tasks:"
php artisan schedule:list | head -5
echo ""

echo "5. Recent Execution Log:"
tail -10 storage/logs/laravel.log
```

### Save as Script:
```bash
nano /var/www/html/it_ticket_system/monitor-scheduler.sh
chmod +x /var/www/html/it_ticket_system/monitor-scheduler.sh
./monitor-scheduler.sh
```

---

## 8. PRODUCTION MONITORING SETUP

### Enable More Detailed Logging:

Edit `.env`:
```
LOG_CHANNEL=stack
LOG_LEVEL=debug  # Change to debug for more details
```

### Log to Separate File (Optional):

Edit `config/logging.php` to create a scheduler-specific channel:
```php
'scheduler' => [
    'driver' => 'single',
    'path' => storage_path('logs/scheduler.log'),
    'level' => 'debug',
],
```

---

## 9. QUICK TROUBLESHOOTING COMMANDS

### If Tasks Not Running:

1. **Verify cron is running:**
   ```bash
   sudo systemctl restart cron
   ```

2. **Check for overlapping executions:**
   ```bash
   tail -50 storage/logs/laravel.log | grep "Another instance"
   ```

3. **Test the schedule:run command manually:**
   ```bash
   php artisan schedule:run -v
   ```

4. **Clear any stuck cache:**
   ```bash
   php artisan cache:clear
   ```

5. **Check file permissions:**
   ```bash
   sudo chown -R www-data:www-data /var/www/html/it_ticket_system/storage
   ```

---

## 10. EXPECTED SCHEDULE TIMES

Your scheduled jobs run every day at:

| Time | Command |
|------|---------|
| 08:00 AM | send-unassigned-reminder |
| 08:01 AM | send-unassigned-deadline-warning |
| 08:02 AM | send-approval-reminders |
| 08:04 AM | send-assigned-reminder |
| 08:06 AM | send-it-manager-reminders |
| 08:07 AM | send-approver-confirmation-reminders |
| 08:08 AM | send-sla-warnings |
| 08:10 AM | send-approval-deadline-warnings |
| 08:12 AM | send-overdue-alerts |
| 08:14 AM | send-completion-confirmations |
| 08:15 AM | send-approval-confirmation-reminders |
| 08:16 AM | send-long-pending-reminder |
| 08:30 AM (Friday only) | send-weekly-summary |

**To verify at 08:00 AM tomorrow:**
```bash
# Run this tomorrow morning
tail -50 storage/logs/laravel.log
```

---

## SUMMARY

**Quick Health Check (Run this daily):**
```bash
cd /var/www/html/it_ticket_system && \
echo "=== CRON STATUS ===" && \
sudo systemctl is-active cron && \
echo "=== NEXT TASKS ===" && \
php artisan schedule:list | head -5 && \
echo "=== RECENT EXECUTIONS ===" && \
tail -5 storage/logs/laravel.log
```
