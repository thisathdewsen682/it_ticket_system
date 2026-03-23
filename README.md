# IT Ticket System

A full-featured, production-ready **IT Service Request Management System** built with Laravel 12. Designed for organizations that need a structured, multi-level approval workflow with automated email notifications, SLA tracking, and role-based dashboards.

Built to digitize the entire lifecycle of IT service requests — from submission to multi-level approval, assignment, completion, and final confirmation — with full audit trails and automated reminders at every stage.

---

## Key Features

### Multi-Level Approval Workflow (5-Stage Pipeline)
- **Employee** submits a ticket → **Department/Section Manager** approves → **IT Department Manager** confirms → **IT Manager** assigns → **IT Member** completes the work
- Each stage supports approval, rejection (with remarks), and reopening
- 15 distinct ticket statuses with full state machine logic
- Tickets can be reopened at multiple levels for rework or reassignment

### Role-Based Access Control (6 Roles + Super Admin)
| Role | Capabilities |
|------|-------------|
| **Employee** | Submit tickets, track progress, confirm completed work |
| **Section Manager** | Approve/reject tickets from their section, self-approve capability |
| **Department Manager** | Approve/reject tickets, receive weekly summaries |
| **IT Department Manager** | Second-level approval, final completion confirmation, reopen jobs |
| **IT Manager** | Assign tickets to IT staff with deadlines and instructions, confirm/reopen work |
| **IT Member** | Work on assigned tickets (start → complete), handle reopened tickets |
| **Super Admin** | Full user CRUD, password management, email log monitoring |

### Multi-Role Support
- Users can hold **multiple roles simultaneously** (e.g., an IT Manager who is also a Department Manager)
- Unified **tabbed dashboard** — switch between role views without logging out
- Roles can be scoped to specific organizational sections

### Automated Email Notification System (34 Email Templates)
- **Event-triggered emails**: instant notifications on approvals, rejections, assignments, completions, and reopenings
- **Scheduled reminders**: daily/weekly automated reminders for pending actions
- **One-click email approval**: signed URL links allowing managers to approve/reject directly from email
- **All emails are queued** via database queue driver for performance
- **Full email logging**: every sent/failed email is tracked with recipient, subject, status, and associated ticket

### SLA Tracking & Deadline Management
- User-specified **"Needed By"** deadline on every ticket
- IT Manager sets **IT completion deadline** on assignment (enforced to be before the ticket deadline)
- Automated **SLA warnings** sent 2 days before deadline
- **Overdue ticket alerts** sent daily to IT managers
- **Long-pending reminders** for tickets in progress 5+ days
- Signed approval URLs expire based on ticket deadlines

### 16 Scheduled Automation Tasks
| Task | Schedule |
|------|----------|
| Pending approval reminders | Daily 08:00 |
| IT Dept Manager approval reminders | Daily |
| Unassigned ticket reminders | Daily |
| Assigned IT member reminders | Daily |
| IT Manager confirmation reminders | Daily |
| IT Dept Manager completion reminders | Daily |
| Approver confirmation reminders | Daily |
| Requester completion reminders | Daily |
| Unassigned deadline warnings | Daily 08:30 |
| SLA deadline warnings | Daily 08:35 |
| Approval deadline warnings | Daily 08:40 |
| Overdue ticket alerts | Daily 08:45 |
| Long-pending reminders | Daily 08:50 |
| Weekly summary to managers | Friday 08:55 |

All scheduled commands use `withoutOverlapping()` and `onOneServer()` for safe concurrent execution.

### File Attachments
- Multi-file upload support on ticket creation
- Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP, TXT
- 10 MB per file limit with secure storage
- Authorization-checked downloads (only requester, approver, and assigned IT staff)

### Full Audit Trail
- Every status change is logged in `ticket_status_histories` with timestamp, user, and optional remark
- Complete email delivery audit via `email_logs` table
- Public ticket tracking page — anyone can check ticket progress by section without logging in

### Super Admin Dashboard
- Create, edit, and delete user accounts
- Auto-generated temporary passwords with welcome emails
- Force password change on first login
- Email monitoring: view sent, pending, and failed emails with retry/flush capabilities

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.2+, Laravel 12 |
| **Frontend** | Blade Templates, Tailwind CSS 3, Alpine.js 3 |
| **Build Tool** | Vite 7 |
| **Database** | SQLite (default) / MySQL / PostgreSQL |
| **Authentication** | Laravel Breeze |
| **Queue** | Database driver (jobs/failed_jobs tables) |
| **Task Scheduling** | Laravel Scheduler (16 automated commands) |
| **Email** | Laravel Mail (queued, with full logging) |

---

## Database Schema

21 migrations covering 11 tables:

| Table | Purpose |
|-------|---------|
| `users` | User accounts with employee number, multi-role support, super admin flag |
| `roles` | Role definitions (employee, section_manager, dept_manager, it-dept-manager, it_manager, it_member) |
| `role_user` | Pivot table for multi-role assignments with optional section scoping |
| `tickets` | Core ticket data — 25+ fields covering all request types and category-specific details |
| `ticket_status_histories` | Full audit trail of every status transition with remarks |
| `ticket_attachments` | File upload metadata (original name, stored path, MIME type, file size) |
| `sections` | Organizational sections (12 pre-seeded departments) |
| `email_logs` | Complete email delivery log with status tracking |
| `jobs` / `failed_jobs` | Laravel queue tables for async email processing |
| `sessions` | Server-side session storage |

### Category-Specific Fields
Tickets capture specialized data based on category:
- **Hardware**: asset tag, device name, IP address
- **Access Requests**: system name, access role, start/end dates
- **Incidents**: incident start time, steps to reproduce, error message, impact assessment

---

## Architecture

```
app/
├── Console/Commands/     # 16 scheduled automation commands
├── Http/
│   ├── Controllers/      # 7 controllers (Ticket, Dashboard, SuperAdmin, EmailLog, etc.)
│   ├── Middleware/        # Role-based access, Super Admin check, Force Password Change
│   └── Requests/         # Form validation
├── Listeners/            # Email event listeners (LogSentEmail, LogFailedEmail)
├── Mail/                 # 34 Mailable classes (event-triggered + scheduled reminders)
├── Models/               # 7 Eloquent models with relationships
└── View/                 # View composers

resources/views/          # 77 Blade templates
├── auth/                 # Authentication views
├── dashboard/            # Role-specific dashboards with tab partials
├── email-logs/           # Email monitoring interface
├── emails/               # 28 email templates with shared layout
├── public/               # Public ticket tracking pages
├── super-admin/          # User management interface
└── tickets/              # Ticket CRUD and approval views
```

---

## Ticket Workflow

```
Employee creates ticket
        │
        ▼
   ┌──────────┐
   │  Pending  │
   └─────┬─────┘
         │
         ▼
  Dept/Section Manager ──── Reject ──► Closed (with remark)
         │
       Approve
         │
         ▼
  IT Dept Manager ────────── Reject ──► Closed (with remark)
         │
       Confirm
         │
         ▼
  IT Manager ─────────────── Reject ──► Closed (with remark)
         │
       Assign (+ deadline + instructions)
         │
         ▼
  IT Member
  Start Work ──► Complete Work
                       │
                       ▼
                 IT Manager
              ┌────┼──────────┐
          Confirm  Reopen   Reject
              │      │
              │      └──► IT Member (rework)
              ▼
        IT Dept Manager
           ┌────┴─────┐
       Confirm      Reopen ──► IT Manager (reassign)
           │
           ▼
     ✅ CLOSED
  (All parties notified automatically)
```

---

## Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & npm
- SQLite / MySQL / PostgreSQL

### Quick Setup

```bash
# Clone the repository
git clone https://github.com/thisathdewsen682/it_ticket_system.git
cd it_ticket_system

# Run the automated setup script
composer setup
```

The `composer setup` command handles everything: dependency installation, environment configuration, key generation, database migration, and frontend build.

### Manual Setup

```bash
# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed default roles and sections
php artisan db:seed

# Install frontend dependencies and build assets
npm install
npm run build

# Create storage symlink
php artisan storage:link
```

### Development Server

```bash
# Start all development services concurrently (app server, queue worker, log viewer, Vite)
composer dev
```

### Queue Worker (Required for Email Delivery)

```bash
php artisan queue:listen --tries=1
```

### Task Scheduler (Required for Automated Reminders)

Add to your server's crontab:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Configuration

### Email
Configure your SMTP settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="IT Ticket System"
```

### Database
Default is SQLite. To use MySQL, update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_ticket_system
DB_USERNAME=root
DB_PASSWORD=your-password
```

---

## Documentation

Comprehensive documentation is available in the `docs/` folder:

| Document | Description |
|----------|-------------|
| [User Manual](docs/USER_MANUAL.md) | Complete guide for all user roles |
| [Email Setup Guide](docs/EMAIL_SETUP_GUIDE.md) | SMTP and email configuration |
| [Cron & Scheduler Guide](docs/CRON_LARAVEL_SCHEDULER_GUIDE.md) | Setting up automated tasks |
| [Scheduled Mail Documentation](docs/SCHEDULED_MAIL_DOCUMENTATION.md) | All automated email details |
| [Maintenance & Monitoring](docs/MAINTENANCE_AND_MONITORING_MANUAL.md) | Production operations guide |
| [Troubleshooting](docs/TROUBLESHOOTING_EXAMPLES.md) | Common issues and solutions |

---

## License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
