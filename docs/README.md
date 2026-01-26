# IT Ticket System - Complete Documentation

Welcome! This folder contains all documentation for the IT Ticket System. Use this guide to find what you need.

---

## 📋 Quick Navigation

### 🚀 Getting Started (READ FIRST!)
- **[Maintenance & Monitoring Manual](MAINTENANCE_AND_MONITORING_MANUAL.md)** - Daily operations, health checks, backups, and troubleshooting
- **[Email Setup Guide](EMAIL_SETUP_GUIDE.md)** - Configure email for the system

### ⚙️ System Administration & Deployment
- **[Cron & Scheduler Guide](CRON_LARAVEL_SCHEDULER_GUIDE.md)** - Deep dive into how scheduled jobs work
- **[Monitoring Scheduled Jobs](MONITORING_SCHEDULED_JOBS.md)** - Check if scheduled tasks are running
- **[Scheduler Setup Complete](SCHEDULER_SETUP_COMPLETE.txt)** - What was configured summary

### 📧 Features & Overview
- **[Automation Overview](README_AUTOMATION.md)** - Email reminder system features
- **[Scheduled Mail Documentation](SCHEDULED_MAIL_DOCUMENTATION.md)** - Mail system details

### 🔧 Troubleshooting & Reference
- **[Quick Reference Guide](CRON_QUICK_REFERENCE.md)** - Emergency commands and quick fixes
- **[Troubleshooting Examples](TROUBLESHOOTING_EXAMPLES.md)** - Real-world scenarios and solutions

---

## 📚 Document Descriptions

| Document | What It Covers | Who Should Read |
|----------|---------------|-----------------|
| **Maintenance & Monitoring Manual** | Daily checks, backups, deployment, emergencies, performance | System operators, DevOps |
| **Cron & Scheduler Guide** | How cron works, Laravel scheduler, technical deep dive | Developers, DevOps |
| **Quick Reference Guide** | Emergency commands, quick fixes, restart procedures | Everyone (bookmark this!) |
| **Troubleshooting Examples** | 10+ real-world problems and solutions | System operators |
| **Email Setup Guide** | Configure SMTP, mail settings, email configuration | System administrators |
| **Automation Overview** | Email reminder features, workflow details | Project managers, developers |
| **Monitoring Scheduled Jobs** | Check if jobs run, view logs, verify tasks | System operators |
| **Scheduler Setup Complete** | What was configured, all settings summary | Reference |

---

## 🎯 By Role

### **System Operator (Daily User)**
Read in this order:
1. [Maintenance & Monitoring Manual](MAINTENANCE_AND_MONITORING_MANUAL.md) - Morning checklist
2. [Quick Reference Guide](CRON_QUICK_REFERENCE.md) - If something breaks
3. [Troubleshooting Examples](TROUBLESHOOTING_EXAMPLES.md) - Common problems

### **System Administrator**
Read in this order:
1. [Maintenance & Monitoring Manual](MAINTENANCE_AND_MONITORING_MANUAL.md) - All operations
2. [Cron & Scheduler Guide](CRON_LARAVEL_SCHEDULER_GUIDE.md) - Understand the system
3. [Email Setup Guide](EMAIL_SETUP_GUIDE.md) - Configure email

### **Developer/DevOps**
Read in this order:
1. [Cron & Scheduler Guide](CRON_LARAVEL_SCHEDULER_GUIDE.md) - Technical details
2. [Automation Overview](README_AUTOMATION.md) - Features overview
3. [Scheduler Setup Complete](SCHEDULER_SETUP_COMPLETE.txt) - Configuration reference

### **Project Manager**
Read in this order:
1. [Automation Overview](README_AUTOMATION.md) - Feature summary
2. [Maintenance & Monitoring Manual](MAINTENANCE_AND_MONITORING_MANUAL.md) - Operational status

---

## 🚨 Emergency? Use This!

If the system is down or something is broken:

1. **First:** Read [Quick Reference Guide](CRON_QUICK_REFERENCE.md) - Has emergency commands
2. **Then:** Check [Maintenance & Monitoring Manual](MAINTENANCE_AND_MONITORING_MANUAL.md) - Emergency Procedures section
3. **Still stuck?** Check [Troubleshooting Examples](TROUBLESHOOTING_EXAMPLES.md) - Similar problems solved

---

## 📅 Scheduled Email Reminders

The system sends **13+ automated emails daily:**

**08:00-08:30 AM (Staggered every 2 minutes):**
- Unassigned ticket reminders (IT Manager)
- Deadline warnings (2 days before due)
- Approval reminders (Approvers)
- Assigned ticket reminders (IT Members)
- IT Manager confirmations (IT Manager)
- Approver confirmations (Approvers)
- SLA warnings (Requesters)
- Approval deadline warnings (Approvers)
- Overdue ticket alerts (IT Manager)
- Completion confirmations (Requesters)
- Requester confirmations (Requesters)
- Long-pending reminders (IT Members)

**08:30 AM Friday:**
- Weekly summary to department managers

For details, see [Automation Overview](README_AUTOMATION.md)

---

## 🔄 Workflow: Complete Ticket Lifecycle

```
1. Requester submits ticket (status: pending)
           ↓
2. IT Member completes work (status: it_completed)
   → Email sent to requester
           ↓
3. IT Manager confirms completion (status: it_mgr_confirmed)
   → Daily reminders to IT Manager
           ↓
4. Department Manager approves (status: dept_approved)
   → IMMEDIATE EMAIL to requester
           ↓
5. Department Manager confirms done (status: dept_confirmed)
   → DAILY REMINDER to requester (08:17 AM)
           ↓
6. Requester confirms ticket resolved (status: resolved/closed)
   → Ticket closed ✓
```

For more details, see [Automation Overview](README_AUTOMATION.md)

---

## 💻 System Information

| Component | Technology | Status |
|-----------|-----------|--------|
| Framework | Laravel 11 | ✅ Active |
| Scheduler | Linux Cron | ✅ Running |
| Database | MySQL/MariaDB | ✅ Configured |
| Email | SMTP | ✅ Configured |
| Backups | Automatic Daily | ✅ Running |
| Monitoring | Log-based | ✅ Enabled |

---

## 📞 Need Help?

1. **For operations questions** → Read [Maintenance & Monitoring Manual](MAINTENANCE_AND_MONITORING_MANUAL.md)
2. **For technical questions** → Read [Cron & Scheduler Guide](CRON_LARAVEL_SCHEDULER_GUIDE.md)
3. **For email issues** → Read [Email Setup Guide](EMAIL_SETUP_GUIDE.md)
4. **For emergencies** → Read [Quick Reference Guide](CRON_QUICK_REFERENCE.md)
5. **Still stuck?** → Contact your IT department

---

## 📝 Quick Commands

```bash
# Check scheduled jobs
php artisan schedule:list

# View application logs
tail -100 storage/logs/laravel.log

# Test an email reminder command
php artisan tickets:send-approval-reminders

# Backup database
mysqldump -u www-data -p database_name | gzip > backup.sql.gz

# Check cron is running
sudo systemctl status cron

# Deploy new code (from main branch)
git checkout main && git pull && php artisan migrate --force
```

---

## 📄 File Organization

```
docs/
├── README.md (this file)
├── MAINTENANCE_AND_MONITORING_MANUAL.md
├── CRON_LARAVEL_SCHEDULER_GUIDE.md
├── MONITORING_SCHEDULED_JOBS.md
├── QUICK_REFERENCE_GUIDE.md
├── TROUBLESHOOTING_EXAMPLES.md
├── EMAIL_SETUP_GUIDE.md
├── AUTOMATION_OVERVIEW.md
├── SCHEDULED_MAIL_DOCUMENTATION.md
└── SCHEDULER_SETUP_COMPLETE.txt
```

---

## ✅ Last Updated

**January 26, 2026**

System Status: ✅ All automated reminders running  
Last Backup: Daily at 2 AM  
Next Scheduled Run: 08:00 AM daily

---

**👉 New to the system? Start with [Maintenance & Monitoring Manual](MAINTENANCE_AND_MONITORING_MANUAL.md)!**
