# Practical Troubleshooting Examples

## Real Scenario 1: "Emails Stopped Working After Server Restart"

### Diagnosis
```bash
# Step 1: Check if cron service is running
sudo systemctl status cron
# If it says "inactive (dead)" → This is the problem!

# Step 2: Check if scheduled job still exists
sudo crontab -u www-data -l
# If empty → Cron service crashed and lost the job!
```

### Solution
```bash
# Restart cron service
sudo systemctl start cron

# Verify it's running
sudo systemctl is-active cron
# Output: active ✓

# Reinstall the cron job (just to be safe)
sudo crontab -u www-data -e

# Make sure this line exists:
* * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1

# Enable auto-start on reboot
sudo systemctl enable cron
```

---

## Real Scenario 2: "Reminders Are Running But Emails Don't Arrive"

### Diagnosis
```bash
# Step 1: Check if command actually ran
tail -20 storage/logs/laravel.log

# Look for:
# "Checking for pending approval tickets..."
# "Reminder sent to manager@example.com"
# OR "No pending approval tickets found"

# If you see those → Command ran successfully!
# But if no emails arrived → Problem is MAIL, not SCHEDULER

# Step 2: Check mail configuration
cat .env | grep MAIL_

# Should show:
# MAIL_DRIVER=smtp
# MAIL_HOST=smtp.mailtrap.io
# MAIL_USERNAME=xxxxx
# MAIL_PASSWORD=xxxxx
# MAIL_FROM_ADDRESS=noreply@example.com
```

### Solution
```bash
# Test mail directly in Laravel
php artisan tinker

# In tinker:
\Illuminate\Support\Facades\Mail::raw(
    'Test email from cron system',
    function($m) {
        $m->to('your-email@example.com')
          ->subject('Cron Test Email');
    }
);

# If email arrives → Mail is working, issue is elsewhere
# If email doesn't arrive → Fix MAIL_* settings in .env

# Exit tinker
exit
```

---

## Real Scenario 3: "Tasks Running Twice or Multiple Times"

### Diagnosis
```bash
# Step 1: Check logs for duplicate executions
grep "tickets:send-approval-reminders" storage/logs/laravel.log | grep "08:02" | wc -l

# If count > 1 for a single time → Duplicate execution!

# Step 2: Check for lock files
ls -la storage/framework/schedule-*

# Each task should have ONE lock file per time
```

### Solution
```bash
# Clear all scheduler locks
php artisan cache:clear
rm -f storage/framework/schedule-*

# Force garbage collection
php artisan schedule:finish-command
```

---

## Real Scenario 4: "Task Says It's Due But Doesn't Run"

### Example Error
```
php artisan schedule:list
# Shows: "Next Due: 30 seconds from now"
# But it never runs...
```

### Diagnosis
```bash
# Step 1: Can the command run manually?
php artisan tickets:send-approval-reminders

# If it works manually but not via cron → Permission issue

# Step 2: Check PHP path in cron
which php
# Output: /usr/bin/php

# Step 3: Test PHP with full path
/usr/bin/php --version
# Should show PHP version

# Step 4: Check if cron can access project files
sudo -u www-data /usr/bin/php /var/www/html/it_ticket_system/artisan schedule:list
# If it works → Cron has access
# If error → File permission problem
```

### Solution
```bash
# Fix file ownership
sudo chown -R www-data:www-data /var/www/html/it_ticket_system

# Fix directory permissions
sudo chmod -R 755 /var/www/html/it_ticket_system
sudo chmod -R 775 /var/www/html/it_ticket_system/storage

# Update cron job with full PHP path
sudo crontab -u www-data -e

# Change from:
# * * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run

# Change to:
# * * * * * cd /var/www/html/it_ticket_system && /usr/bin/php artisan schedule:run
```

---

## Real Scenario 5: "Everything Looks Good But Tasks Seem to Skip Sometimes"

### Diagnosis
```bash
# Step 1: Check system time vs scheduler time
date
# Output: Mon Jan 26 08:02:45 AM +0530 2026
#         ↑ Must match scheduled task time exactly

# Step 2: Check for time drift
# (If server time is way off)

# Step 3: Check for 'withoutOverlapping()' issues
grep "without.*overlap\|onOneServer" routes/console.php
# If many tasks use it, one slow task can block others
```

### Solution
```bash
# Sync system time with NTP server
sudo ntpdate -s time.nist.gov

# OR if using systemd-timesyncd:
sudo systemctl restart systemd-timesyncd

# Verify time is now correct:
date

# Clear any stuck locks:
php artisan cache:clear
```

---

## Real Scenario 6: "Need to Temporarily Disable a Task"

### Quick Disable (Temporary)
```bash
# Edit routes/console.php
nano routes/console.php

# Comment out the task:
// Schedule::command('tickets:send-approval-reminders')
//     ->dailyAt('08:02')
//     ->withoutOverlapping()
//     ->onOneServer();

# Save (Ctrl+X, then Y, then Enter)

# No cache clear needed - changes apply immediately
```

### Permanent Disable (If Task Is Broken)
```bash
# Option 1: Comment it out (as above)

# Option 2: Add condition to skip it
Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('08:02')
    ->withoutOverlapping()
    ->onOneServer()
    ->skip(function () {
        // Disable until Feb 1
        return now()->lessThan('2026-02-01');
    });
```

---

## Real Scenario 7: "Debugging: Need to See Exactly What's Happening"

### Enable Verbose Logging
```bash
# Edit routes/console.php and add callbacks:

Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('08:02')
    ->withoutOverlapping()
    ->onOneServer()
    ->before(function () {
        \Log::info('⏰ Starting approval reminders at ' . now());
    })
    ->after(function () {
        \Log::info('✅ Approval reminders completed at ' . now());
    })
    ->onFailure(function () {
        \Log::error('❌ Approval reminders FAILED at ' . now());
    });
```

### Real-Time Log Watching
```bash
# Watch logs as tasks execute (in real-time)
tail -f storage/logs/laravel.log

# In another terminal, at task time, run:
php artisan schedule:run --verbose

# The tail window will show all output instantly
```

---

## Real Scenario 8: "Changed Something and Now Nothing Works"

### Step-by-Step Recovery
```bash
# 1. Check what changed
git diff                          # If using git
nano routes/console.php           # Review scheduler config
nano .env                         # Check configuration

# 2. Test basic functionality
php artisan tinker
  \Artisan::call('list');
  exit

# 3. Test a single command
php artisan tickets:send-approval-reminders

# 4. Check for syntax errors
php artisan schedule:list
# If error → Syntax problem in routes/console.php

# 5. Clear everything
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# 6. Test again
php artisan schedule:list

# 7. If still broken, revert the change
git checkout routes/console.php    # If using git
# OR manually restore from backup
```

---

## Real Scenario 9: "One Task Is Broken, Others Still Work"

### Strategy
```bash
# Find which task is broken
# Check logs:
tail -50 storage/logs/laravel.log | grep -A3 "error\|failed\|exception"

# Identify the failing command, then:

# Option A: Fix the bug (if you can)
# Edit the command file: app/Console/Commands/SendXXX.php
# Debug and fix the issue

# Option B: Disable just that one task
nano routes/console.php
# Comment out just that command
# Leave others running

# Option C: Redirect its output for easier debugging
# Edit routes/console.php:
Schedule::command('broken-command')
    ->dailyAt('08:02')
    ->sendOutputTo(storage_path('logs/broken-command.log'));

# Then check that specific log:
tail -50 storage/logs/broken-command.log
```

---

## Real Scenario 10: "Production Emergency - Disable All Scheduled Tasks NOW"

### Emergency Stop
```bash
# Option 1: Stop cron service entirely
sudo systemctl stop cron

# All tasks stop running immediately
# System will continue to work, just no automatic tasks

# Option 2: Disable just the Laravel scheduler job
sudo crontab -u www-data -e
# Comment out the schedule:run line:
# * * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run

# Option 3: Disable in code (fastest if already SSH'd in)
nano routes/console.php
# Comment out problematic command(s)
```

### Recovery (Re-enable)
```bash
# After fixing the issue:

# Restart cron
sudo systemctl start cron

# OR uncomment the line
sudo crontab -u www-data -e
# Uncomment: * * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run

# OR fix the code
nano routes/console.php
# Uncomment the line

# Verify
sudo systemctl is-active cron
php artisan schedule:list
```

---

## Debugging Command Cheat Sheet

```bash
# View current scheduled tasks
php artisan schedule:list

# Run scheduler with detailed output
php artisan schedule:run --verbose

# Test a specific command
php artisan tickets:send-approval-reminders

# Interactive debugging
php artisan tinker

# Check recent errors
tail -100 storage/logs/laravel.log | grep -i error

# View cron execution logs
sudo grep CRON /var/log/syslog | tail -20

# Check system time
date

# Verify permissions
ls -la storage/logs/
sudo -u www-data php artisan schedule:list

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Reinstall cron job
sudo crontab -u www-data -e
# Add: * * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1
```

---

## When to Escalate

**Contact hosting provider if:**
- Cron service won't start: `sudo systemctl start cron` fails
- Permission errors: `sudo chown` doesn't help
- Time is completely wrong and NTP doesn't fix it
- Server resources maxed out causing slowdowns
- Mail server connectivity issues

**Contact Laravel community if:**
- PHP syntax errors in routes/console.php
- Database connection issues in commands
- Complex business logic bugs in commands

---

**Remember:** Always check logs first, test manually second, then consider the infrastructure.
