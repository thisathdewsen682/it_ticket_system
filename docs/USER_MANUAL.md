# IT Ticket System — User Manual

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Employee (Requester)](#1-employee-requester)
3. [Department Manager / Section Manager](#2-department-manager--section-manager)
4. [IT Department Manager](#3-it-department-manager)
5. [IT Manager](#4-it-manager)
6. [IT Member (IT Staff)](#5-it-member-it-staff)
7. [Super Admin](#6-super-admin)
8. [Ticket Status Reference](#ticket-status-reference)

---

## Getting Started

### Logging In

1. Open the system URL in your browser.
2. Enter your **email** and **password**.
3. Click **Login**.
4. If this is your first login, you will be asked to **change your password** before continuing.

### Navigation

- After login you are taken to your **Dashboard** automatically based on your role.
- If you have **multiple roles**, you will see a **tabbed dashboard** — click the tabs to switch between your role views.

---

## 1. Employee (Requester)

> You submit IT requests and track their progress. You receive email notifications at every stage.

### Creating a Ticket

1. Go to your **Employee Dashboard**.
2. Fill in the ticket form:
   - **Title** — short description of what you need.
   - **Description** — explain the request in detail.
   - **Category** — select the type (e.g. Hardware, Software, Access, Incident).
   - **Priority** — Low, Medium, High, or Critical.
   - **Needed By** — the date you need this completed.
   - **Approver** — select your Department Manager or Section Manager.
   - **Section** — your organizational section.
   - **Attachments** — upload any supporting files (screenshots, documents).
3. Fill in any additional fields relevant to your request type (device info, access details, incident details).
4. Click **Submit**.

### Tracking Your Tickets

1. Go to **My Tickets** to see all your submitted tickets.
2. Each ticket shows its current **status** (see [Status Reference](#ticket-status-reference) below).
3. Click on a ticket to view full details and status history.

### Email Notifications You Receive

**Status Updates:**
- Your ticket has been approved by the department manager.
- Your ticket has been rejected (by department manager, IT Dept Manager, or IT Manager) — includes reason.
- Your job has been completed and confirmed — the ticket is closed.

**Alerts:**
- SLA deadline warning — sent 2 days before your ticket's "Needed By" date if still in progress.

### When Your Ticket Is Completed

Once the IT team finishes the work and it is confirmed by the IT Department Manager, you will receive an **email notification** that your job is complete. No further action is needed from you — the ticket is automatically closed.

---

## 2. Department Manager / Section Manager

> You approve or reject IT requests from your team, and confirm completed work.

### Approving / Rejecting Tickets

You can approve or reject tickets in **two ways**:

**Option A: Via Email Link (Quick)**
1. Open the approval request email you received.
2. Click the **Approve** or **Reject** link directly in the email.
3. You will be taken to the system and the action is applied immediately.

**Option B: Via Dashboard**
1. Go to your **Manager Dashboard**.
2. Open the **Pending** tab — these are tickets waiting for your decision.
3. Click on a ticket to review it.
4. Choose one of:
   - **Approve** — the ticket moves forward to the IT Department Manager.
   - **Reject** — enter a **remark** explaining why, and the requester is notified.

### Monitoring Approved Tickets

- Open the **Approved** tab to see tickets currently being worked on.
- Open the **Completed** tab to see finished tickets.
- Open the **Rejected** tab to see tickets you have rejected.

### Confirming Completed Work

After the IT team finishes a job and the IT Dept Manager confirms it, you will receive an **email notification** that the work is complete. No further action is required from you.

### Email Notifications You Receive

**Action Required:**
- New ticket submitted to you for approval (includes one-click approve/reject links).
- Reminder emails for tickets still pending your approval.
- Warning when an approval deadline is approaching (2 days before deadline).

**Informational:**
- Notification when IT Dept Manager rejects a ticket you approved.
- Notification when IT Manager rejects a ticket.
- Notification when the job is completed and confirmed by IT Dept Manager.
- Weekly summary report of all tickets in your sections (sent on Fridays).

---

## 3. IT Department Manager

> You review manager-approved tickets before they reach the IT team, and give the final confirmation when work is complete.

### Reviewing Approved Tickets

1. Go to your **Dashboard** (IT Dept Manager tab if you have multiple roles).
2. Find tickets with status **Manager Approved** — these need your review.
3. Choose one of:
   - **Confirm** — the ticket is forwarded to the IT Manager for assignment.
   - **Reject** — enter a remark, the requester and approver are notified.

### Confirming Final Completion

After the IT Manager confirms a job is done:

1. You receive an **email notification**.
2. Open the ticket and review the work.
3. Choose one of:
   - **Confirm Completion** — the job is officially complete. The requester and department manager are notified automatically. **This is the final step — no further confirmation is needed from anyone.**
   - **Reopen** — enter a remark, the ticket goes back to the IT Manager for reassignment.

### Email Notifications You Receive

**Action Required:**
- New ticket forwarded from department managers (awaiting your review).
- Daily reminder for tickets still awaiting your approval (`dept_approved` status).
- IT Manager has confirmed work completion — needs your final confirmation.
- Daily reminder for tickets awaiting your completion confirmation.

**Informational:**
- Notification when IT Manager rejects a ticket.

---

## 4. IT Manager

> You assign tickets to IT staff, monitor progress, and confirm completed work.

### Assigning Tickets

1. Go to your **IT Manager Dashboard**.
2. Open the **Approved** tab — these are tickets ready for assignment.
3. Click on a ticket and:
   - Select an **IT Member** from the dropdown.
   - Set an **IT Deadline** (must be before the ticket's "Needed By" date).
   - Add **IT Instructions** if the IT member needs special guidance.
4. Click **Assign**. The IT member receives an email notification.

### Monitoring Work in Progress

- **Assigning** tab — tickets currently being worked on by IT staff.
- **Reopened** tab — tickets returned by managers or requesters that need reassignment.

### Confirming Completed Work

When an IT member marks their work as complete:

1. Open the **Pending Confirmation** tab.
2. Review the completed work.
3. Choose one of:
   - **Confirm** — moves to IT Department Manager for their confirmation.
   - **Reopen** — enter a remark explaining what needs to be redone. The IT member is notified.
   - **Reject** — if the ticket should not have been worked on. The requester, approver, and IT Dept Managers are notified.

### Reassigning Reopened Tickets

When a ticket is reopened (by department manager, requester, or IT Dept Manager):

1. Open the **Reopened** tab.
2. Reassign to an IT member (can be the same or different person).
3. Set a new deadline and add instructions if needed.

### Email Notifications You Receive

**Action Required:**
- New ticket approved by IT Dept Manager and ready for assignment.
- IT member has completed work — needs your confirmation.
- Ticket reopened by department manager, requester, or IT Dept Manager — needs reassignment.
- Daily reminder for approved but unassigned tickets.
- Daily reminder for tickets awaiting your confirmation.

**Alerts:**
- Overdue ticket alerts (tickets past their "Needed By" date).

---

## 5. IT Member (IT Staff)

> You perform the actual IT work on assigned tickets.

### Viewing Your Assignments

1. Go to your **IT Member Dashboard**.
2. Open the **Assigning** tab to see your assigned tickets.
3. Click on a ticket to view full details, IT instructions, and deadline.

### Working on a Ticket

1. Click **Start Work** to indicate you have begun (status changes to "In Progress").
2. Perform the required IT work.
3. When finished, click **Mark as Completed**.
4. The IT Manager is notified and will review your work.

### Handling Reopened Tickets

If your work is sent back for revision:

1. Open the **Reopened** tab.
2. Read the remark explaining what needs fixing.
3. Click **Start Work** again, make corrections, and mark as completed.

### Tracking Your History

- **Completed** tab — tickets you finished that are pending confirmations.
- **Confirmed** tab — tickets fully confirmed and closed.
- **Rejected** tab — tickets that were rejected at any stage.

### Email Notifications You Receive

**Action Required:**
- New ticket assigned to you by IT Manager.
- Daily reminder for assigned tickets not yet completed.
- Ticket reopened for rework — includes remark explaining what to fix.

**Alerts:**
- Warning for tickets assigned to you for more than 5 days without completion.

---

## 6. Super Admin

> You manage users and monitor the email system.

### Managing Users

1. Go to **Super Admin > Users**.
2. You can:
   - **Create User** — fill in name, email, role, and section. A temporary password is generated automatically and a welcome email is sent.
   - **Edit User** — change name, email, role, section, or super admin status.
   - **Delete User** — permanently remove a user.
   - **Change Password** — force a password reset for any user.

### Monitoring Emails

1. Go to **Email Logs**.
2. View tabs:
   - **Sent** — successfully delivered emails.
   - **Pending** — emails in the queue waiting to be sent.
   - **Failed** — emails that could not be delivered.
3. For failed emails:
   - **Retry** — attempt to resend a single email.
   - **Retry All** — resend all failed emails.
   - **Delete** — remove a failed email from the log.
   - **Flush All** — clear all failed emails.

---

## Ticket Status Reference

| Status | What It Means | Who Acts Next |
|--------|--------------|---------------|
| **Pending** | Ticket submitted, waiting for manager approval | Dept/Section Manager |
| **Manager Approved** | Manager approved, sent to IT Dept Manager | IT Dept Manager |
| **Manager Rejected** | Manager rejected the request | — (Closed) |
| **IT Dept Approved** | IT Dept Manager approved, ready for IT assignment | IT Manager |
| **IT Dept Rejected** | IT Dept Manager rejected the request | — (Closed) |
| **Assigned to IT** | IT Manager assigned to an IT member | IT Member |
| **In Progress** | IT member is working on it | IT Member |
| **IT Completed** | IT member finished, waiting for IT Manager review | IT Manager |
| **IT Manager Confirmed** | IT Manager confirmed, waiting for IT Dept Manager | IT Dept Manager |
| **IT Dept Confirmed** | IT Dept Manager confirmed completion — ticket fully closed | — (Closed) |
| **IT Manager Rejected** | IT Manager rejected the ticket | — (Closed) |
| **Reopened (by IT Manager)** | Sent back to IT member for corrections | IT Member |
| **Reopened (by Dept Manager)** | Dept Manager reopened before IT Dept confirmation, back to IT Manager | IT Manager |
| **Reopened (by IT Dept Manager)** | IT Dept Manager sent back for reassignment | IT Manager |

---

## Ticket Workflow Diagram

```
Employee creates ticket
        │
        ▼
   ┌─────────┐
   │ Pending  │
   └────┬─────┘
        │
        ▼
  Dept/Section Manager
   Approve or Reject?
   ┌────┴─────┐
   │          │
Approve    Reject ──► Closed
   │
   ▼
  IT Dept Manager
   Confirm or Reject?
   ┌────┴─────┐
   │          │
Confirm    Reject ──► Closed
   │
   ▼
  IT Manager
  Assigns to IT Member
   │
   ▼
  IT Member
  Starts Work ──► Completes Work
                        │
                        ▼
                  IT Manager
                  Confirm, Reopen, or Reject?
                  ┌────┼──────────┐
                  │    │          │
              Confirm  Reopen   Reject ──► Closed
                  │    │
                  │    └──► IT Member (redo)
                  ▼
            IT Dept Manager
            Confirm or Reopen?
            ┌────┴─────┐
            │          │
        Confirm    Reopen ──► IT Manager (reassign)
            │
            ▼
  CLOSED (Requester & Approver notified automatically)
```

---

## Public Ticket Tracking

Anyone (even without logging in) can check ticket progress:

1. Go to the **Status** page on the system.
2. Browse tickets by **section** and **status**.
3. Click on a ticket to see its full status history.

---

## Tips

- **Check your email** — the system sends notifications for every important action.
- **Add remarks** when rejecting or reopening — this helps others understand what needs to change.
- **Attach files** when creating tickets — screenshots and documents help IT staff resolve issues faster.
- **Set realistic deadlines** — the "Needed By" date drives reminders and escalations.
- **Don't ignore reminders** — pending tickets delay the entire workflow.
