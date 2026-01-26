# Email System Setup Guide

## 📧 How the Email System Works

### 1. **Initial Approval Email** (Already Working)
When someone creates a ticket:
- User fills out the ticket form and selects an approval person
- System validates the approval person has an email
- **Email is sent immediately** with approve/reject links
- Links are signed and expire at the end of the job completion deadline

### 2. **Daily Reminder Emails** (NEW - Just Added)
For pending approvals:
- Every day at 9:00 AM, system checks for pending tickets
- Sends reminder emails to approval persons who haven't responded
- **Only sends if deadline hasn't passed yet**
- Uses same approve/reject links (they're still valid until deadline)

---

## 🔧 Email Configuration

### Option 1: Gmail/Google Workspace (RECOMMENDED)
1. **Enable 2-Step Verification** on your Google account
2. **Generate App Password**:
   - Go to: https://myaccount.google.com/apppasswords
   - Create app password for "Mail"
   - Copy the 16-character password
3. **Update `.env` file**:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=thisath@kohoku.lk
   MAIL_PASSWORD=your_16_char_app_password_here
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="thisath@kohoku.lk"
   MAIL_FROM_NAME="IT Ticket System"
   ```

### Option 2: Other SMTP Server
Update `.env` with your SMTP details:
```
MAIL_MAILER=smtp
MAIL_HOST=your.smtp.server
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="thisath@kohoku.lk"
MAIL_FROM_NAME="IT Ticket System"
```

### Option 3: Testing Only (Logs to File)
For testing without sending real emails:
```
MAIL_MAILER=log
```
Emails will be saved to: `storage/logs/laravel.log`

---

## ⏰ Setting Up Daily Reminders (Cron Job)

### Step 1: Add to Crontab
Run this command:
```bash
sudo crontab -e
```

Add this line:
```
* * * * * cd /var/www/html/it_ticket_system && php artisan schedule:run >> /dev/null 2>&1
```

This runs Laravel's scheduler every minute, which checks if any tasks need to run.

### Step 2: Verify Cron is Working
```bash
# Check cron service is running
sudo systemctl status cron

# View scheduled tasks
php artisan schedule:list
```

---

## 🧪 Testing the System

### Test 1: Manual Send Reminder Command
```bash
cd /var/www/html/it_ticket_system
php artisan tickets:send-approval-reminders
```

You'll see output like:
```
Checking for pending approval tickets...
Ticket #5: Reminder sent to manager@example.com
Ticket #7: Reminder sent to supervisor@example.com

Summary: 2 reminders sent, 0 failed.
```

### Test 2: Test Email Configuration
```bash
php artisan tinker
```
Then run:
```php
Mail::raw('Test email from IT Ticket System', function ($message) {
    $message->to('thisath@kohoku.lk')->subject('Test Email');
});
```

### Test 3: Create a Test Ticket
1. Log in as an employee
2. Create a ticket and select approval person
3. Check if approval email arrives
4. Next day at 9 AM, check if reminder arrives (or run manual command)

---

## 📋 Email Flow Diagram

```
Employee Creates Ticket
        ↓
Selects Approval Person + Due Date
        ↓
[IMMEDIATE] Approval Request Email Sent
        ↓
    Is Approved?
        ↓ NO (still pending)
[DAILY 9AM] Reminder Email Sent
        ↓
    Is Approved?
        ↓ NO (still pending)
[NEXT DAY 9AM] Reminder Email Sent
        ↓
    ... continues until approved or deadline passes
        ↓
    Deadline Passed?
        ↓ YES
[STOP] No more reminders sent
Approval links expire
```

---

## 🎯 What Happens in Each Scenario

### Scenario 1: Quick Approval
- Day 1, 10 AM: Ticket created → Email sent
- Day 1, 11 AM: Manager approves → ✓ Done
- Day 2, 9 AM: No reminder (already approved)

### Scenario 2: Needs Reminders
- Day 1, 10 AM: Ticket created → Email sent
- Day 2, 9 AM: Still pending → Reminder #1 sent
- Day 3, 9 AM: Still pending → Reminder #2 sent
- Day 3, 2 PM: Manager approves → ✓ Done
- Day 4, 9 AM: No reminder (already approved)

### Scenario 3: Deadline Passes
- Day 1: Ticket created, due date = Day 5
- Day 2, 9 AM: Reminder sent
- Day 3, 9 AM: Reminder sent
- Day 4, 9 AM: Reminder sent
- Day 5, 9 AM: Reminder sent
- Day 6, 9 AM: No reminder (deadline passed)
- Approval links no longer work

---

## 🔍 Troubleshooting

### Emails Not Sending?

1. **Check mail configuration**:
   ```bash
   php artisan config:cache
   php artisan queue:work --once
   ```

2. **View logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Test SMTP connection**:
   ```bash
   telnet smtp.gmail.com 587
   ```

### Reminders Not Running?

1. **Check cron is set up**:
   ```bash
   sudo crontab -l
   ```

2. **Check scheduled tasks**:
   ```bash
   php artisan schedule:list
   ```

3. **Run manually to see errors**:
   ```bash
   php artisan tickets:send-approval-reminders
   ```

### Wrong Time Zone?

Update `.env`:
```
APP_TIMEZONE=Asia/Colombo
```

Then:
```bash
php artisan config:cache
```

---

## 🛠️ Customization

### Change Reminder Time
Edit `routes/console.php`:
```php
Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('09:00')  // Change to '14:00' for 2 PM
```

### Send Multiple Times Per Day
```php
Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('09:00');  // Morning reminder

Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('14:00');  // Afternoon reminder
```

### Only Send on Weekdays
```php
Schedule::command('tickets:send-approval-reminders')
    ->dailyAt('09:00')
    ->weekdays();
```

---

## 📝 Important Notes

1. **First email is sent immediately** when ticket is created
2. **Reminders are daily** at 9:00 AM
3. **Reminders stop** if:
   - Ticket is approved/rejected
   - Deadline has passed
   - Approval person has no email
4. **Links expire** at end of day of the job completion deadline
5. **Cron job must be set up** for reminders to work automatically

---

## ✅ Quick Setup Checklist

- [ ] Update `.env` with email credentials
- [ ] Run `php artisan config:cache`
- [ ] Test email: `php artisan tickets:send-approval-reminders`
- [ ] Add cron job: `sudo crontab -e`
- [ ] Verify cron: `php artisan schedule:list`
- [ ] Create test ticket and check emails
- [ ] Monitor logs: `tail -f storage/logs/laravel.log`

---

**Need Help?** Check the logs in `storage/logs/laravel.log` for detailed error messages.
