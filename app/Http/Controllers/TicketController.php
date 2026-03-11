<?php

namespace App\Http\Controllers;

use App\Mail\TicketApprovalRequestMail;
use App\Mail\TicketApprovedNotifyItManagerMail;
use App\Mail\TicketApprovedNotifyItDeptManagerMail;
use App\Mail\TicketReopenedByItManagerMail;
use App\Mail\TicketReopenedByDeptManagerMail;
use App\Mail\TicketItManagerConfirmedMail;
use App\Mail\TicketDeptConfirmedNotifyRequesterMail;
use App\Mail\TicketReopenedByRequesterMail;
use App\Models\Section;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    private function recordStatusChange(Ticket $ticket, ?int $userId, ?string $from, string $to, ?string $remark = null): void
    {
        TicketStatusHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'from_status' => $from,
            'to_status' => $to,
            'remark' => $remark,
        ]);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $user = $request->user()->load('role');

        $allowed = false;

        if ($user && ($user->hasRole('it_manager') || $user->hasRole('it-dept-manager') || $user->hasRole('dept_manager') || $user->hasRole('section_manager'))) {
            $allowed = true;
        }

        if ($ticket->requester_id === $user->id || $ticket->approval_user_id === $user->id || $ticket->it_member_id === $user->id) {
            $allowed = true;
        }

        if (!$allowed) {
            abort(403);
        }

        $ticket->load([
            'requester:id,name',
            'approvalUser:id,name',
            'itMember:id,name',
            'statusHistories.user:id,name',
            'attachments',
        ]);

        return view('tickets.show', compact('ticket'));
    }

    public function publicSectionStatus(Request $request)
    {
        $statusLabels = [
            'pending' => 'Pending approval',
            'dept_approved' => 'Manager approved',
            'it_assigned' => 'Assigned to IT',
            'it_in_progress' => 'In progress',
            'it_completed' => 'Awaiting IT manager confirmation',
            'it_mgr_confirmed' => 'IT manager confirmed',
            'dept_confirmed' => 'Department confirmed',
            'requester_confirmed' => 'Requester confirmed',
            'it_reopened' => 'Reopened to IT',
            'dept_reopened' => 'Reopened by Manager',
            'requester_reopened' => 'Reopened by Requester',
            'dept_rejected' => ' Department Rejected',
        ];

        $validated = $request->validate([
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'status' => ['nullable', 'string', Rule::in(array_keys($statusLabels))],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $ticketsQuery = Ticket::query()
            ->select(['id', 'title', 'status', 'priority', 'section_id', 'needed_by', 'updated_at'])
            ->with('section:id,name')
            ->orderByDesc('updated_at');

        if (!empty($validated['section_id'])) {
            $ticketsQuery->where('section_id', $validated['section_id']);
        }

        if (!empty($validated['status'])) {
            $ticketsQuery->where('status', $validated['status']);
        }

        if (!empty($validated['search'])) {
            $ticketsQuery->where('title', 'like', '%' . $validated['search'] . '%');
        }

        $tickets = $ticketsQuery->paginate(15)->withQueryString();

        $sections = Section::orderBy('name')->get(['id', 'name']);

        return view('public.section_status', [
            'tickets' => $tickets,
            'sections' => $sections,
            'statuses' => $statusLabels,
            'selectedSection' => $validated['section_id'] ?? null,
            'selectedStatus' => $validated['status'] ?? null,
            'search' => $validated['search'] ?? null,
        ]);
    }

    public function publicTicketHistory(Ticket $ticket)
    {
        $ticket->load(['section:id,name']);

        $histories = TicketStatusHistory::query()
            ->where('ticket_id', $ticket->id)
            ->with('user:id,name')
            ->orderBy('created_at')
            ->get(['id', 'ticket_id', 'user_id', 'from_status', 'to_status', 'remark', 'created_at']);

        $statusLabels = [
            'pending' => 'Pending approval',
            'dept_approved' => 'Manager approved',
            'it_assigned' => 'Assigned to IT',
            'it_in_progress' => 'In progress',
            'it_completed' => 'Awaiting IT manager confirmation',
            'it_mgr_confirmed' => 'IT manager confirmed',
            'dept_confirmed' => 'Department confirmed',
            'requester_confirmed' => 'Requester confirmed',
            'it_reopened' => 'Reopened to IT',
            'dept_reopened' => 'Reopened by Manager',
            'requester_reopened' => 'Reopened by Requester',
            'dept_rejected' => 'Rejected',
        ];

        return view('public.ticket_history', [
            'ticket' => $ticket,
            'histories' => $histories,
            'statuses' => $statusLabels,
        ]);
    }

    public function index(Request $request)
    {
        $baseQuery = Ticket::query()
            ->with(['approvalUser:id,name', 'itMember:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->where('requester_id', $request->user()->id)
            ->orderByDesc('id');

        // Pending: Waiting for approval or approved but not yet started by IT
        $pendingTickets = (clone $baseQuery)
            ->whereIn('status', [
                'pending',
                'dept_approved',
                'it_dept_approved',
                'it_assigned',
                'it_reopened',
                'dept_reopened',
                'requester_reopened',
            ])
            ->paginate(10, ['*'], 'pending_page')
            ->appends(['tab' => 'pending']);

        // In Progress: Actually being worked on by IT
        $activeTickets = (clone $baseQuery)
            ->whereIn('status', [
                'it_in_progress',
                'it_completed',
                'it_mgr_confirmed',
                'dept_confirmed',
            ])
            ->paginate(10, ['*'], 'active_page')
            ->appends(['tab' => 'active']);

        // Completed: Fully closed
        $completedTickets = (clone $baseQuery)
            ->whereIn('status', [
                'requester_confirmed',
                'it_dept_confirmed_completion',
            ])
            ->paginate(10, ['*'], 'completed_page')
            ->appends(['tab' => 'completed']);

        // Rejected: All rejected jobs
        $rejectedTickets = (clone $baseQuery)
            ->whereIn('status', [
                'dept_rejected',
                'it_dept_rejected',
                'it_manager_rejected',
            ])
            ->paginate(10, ['*'], 'rejected_page')
            ->appends(['tab' => 'rejected']);

        return view('tickets.index', compact('pendingTickets', 'activeTickets', 'completedTickets', 'rejectedTickets'));

        return view('tickets.index', compact('pendingTickets', 'activeTickets', 'completedTickets'));
    }

    public function approvals(Request $request)
    {
        $tickets = Ticket::query()
            ->with(['requester:id,name', 'approvalUser:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->where('approval_user_id', $request->user()->id)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->paginate(10);

        return view('tickets.approvals', compact('tickets'));
    }

    public function approve(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->approval_user_id !== $request->user()->id) {
            abort(403);
        }

        if ($ticket->status !== 'pending') {
            return back()->withErrors(['error' => 'This ticket is no longer pending approval.']);
        }

        if ($ticket->needed_by && now()->greaterThan($ticket->needed_by->copy()->endOfDay())) {
            return back()->withErrors(['error' => 'Approval deadline has passed. The job completion deadline was ' . $ticket->needed_by->format('F j, Y') . '.']);
        }

        $from = $ticket->status;

        // Keep status values short (the DB column is limited).
        $ticket->update(['status' => 'dept_approved']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'dept_approved');

        // Send notification to IT Dept Manager
        $itDeptManager = User::query()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'it-dept-manager');
            })
            ->orWhereHas('role', function ($query) {
                $query->where('name', 'it-dept-manager');
            })
            ->first();

        if ($itDeptManager) {
            $ticket->load(['requester', 'approvalUser']);
            Mail::to($itDeptManager->email)->queue(new TicketApprovedNotifyItDeptManagerMail($ticket));
        }

        return back()->with('status', 'Ticket approved and sent to IT Department Manager.');
    }

    public function reject(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->approval_user_id !== $request->user()->id) {
            abort(403);
        }

        if ($ticket->status !== 'pending') {
            return back()->withErrors(['error' => 'This ticket is no longer pending approval.']);
        }

        if ($ticket->needed_by && now()->greaterThan($ticket->needed_by->copy()->endOfDay())) {
            return back()->withErrors(['error' => 'Rejection deadline has passed. The job completion deadline was ' . $ticket->needed_by->format('F j, Y') . '.']);
        }

        $validated = $request->validate([
            'remark' => ['required', 'string', 'max:2000'],
        ]);

        $from = $ticket->status;

        // Keep status values short (the DB column is limited).
        $ticket->update(['status' => 'dept_rejected']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'dept_rejected', $validated['remark']);

        $ticket->loadMissing(['requester', 'approvalUser']);
        $rejectedBy = 'Department/Section Manager: ' . ($request->user()->name ?? 'Manager');
        if ($ticket->requester && $ticket->requester->email) {
            Mail::to($ticket->requester->email)->queue(
                new \App\Mail\TicketRejectedByDeptManagerMail($ticket, $validated['remark'], $rejectedBy, $ticket->requester->name ?? null)
            );
        }

        return back()->with('status', 'Ticket rejected.');
    }

    // Department/Section Manager confirms job completion
    public function deptConfirmCompletion(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->approval_user_id !== $request->user()->id) {
            abort(403);
        }

        if ($ticket->status !== 'it_dept_confirmed_completion') {
            return back()->withErrors(['error' => 'This ticket is not awaiting your completion confirmation.']);
        }

        $from = $ticket->status;

        $ticket->update(['status' => 'dept_confirmed']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'dept_confirmed', 'Completion confirmed by Department Manager');

        // Send notification to job requester (person who submitted ticket)
        $ticket->loadMissing(['requester', 'approvalUser', 'itMember']);
        
        if ($ticket->requester && $ticket->requester->email) {
            Mail::to($ticket->requester->email)->later(now(), new \App\Mail\CompletionConfirmationMail($ticket, $request->user()));
        }

        return back()->with('status', 'Job completion confirmed. Requester has been notified.');
    }

    // IT Department Manager confirms ticket and sends to IT Manager
    public function itDeptManagerConfirm(Request $request, Ticket $ticket): RedirectResponse
    {
        // Check if user has IT Dept Manager role
        if (!$request->user()->hasRole('it-dept-manager')) {
            abort(403);
        }

        if ($ticket->status !== 'dept_approved') {
            return back()->withErrors(['error' => 'This ticket is not awaiting IT Department Manager confirmation.']);
        }

        $from = $ticket->status;

        // Update status to it_dept_approved
        $ticket->update(['status' => 'it_dept_approved']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_dept_approved', 'Confirmed by IT Dept Manager');

        // Send notification to IT Manager
        $itManager = User::whereHas('role', function ($query) {
            $query->where('name', 'it_manager');
        })->first();

        if ($itManager) {
            $ticket->load(['requester', 'approvalUser']);
            Mail::to($itManager->email)->queue(new TicketApprovedNotifyItManagerMail($ticket));
        }

        return back()->with('status', 'Ticket confirmed and sent to IT Manager for assignment.');
    }

    // IT Department Manager rejects ticket
    public function itDeptManagerReject(Request $request, Ticket $ticket): RedirectResponse
    {
        // Check if user has IT Dept Manager role
        if (!$request->user()->hasRole('it-dept-manager')) {
            abort(403);
        }

        if ($ticket->status !== 'dept_approved') {
            return back()->withErrors(['error' => 'This ticket is not awaiting IT Department Manager confirmation.']);
        }

        $validated = $request->validate([
            'remark' => ['required', 'string', 'max:2000'],
        ]);

        $from = $ticket->status;

        // Update status to it_dept_rejected
        $ticket->update(['status' => 'it_dept_rejected']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_dept_rejected', $validated['remark']);

        // Notify requester
        $ticket->loadMissing(['requester', 'approvalUser']);
        $rejectedBy = 'IT Department Manager: ' . ($request->user()->name ?? 'Manager');
        $requesterEmail = $ticket->requester?->email;
        $approverEmail = $ticket->approvalUser?->email;

        if ($requesterEmail) {
            Mail::to($requesterEmail)->queue(
                new \App\Mail\TicketRejectedByItDeptManagerMail($ticket, $validated['remark'], $rejectedBy, $ticket->requester->name ?? null)
            );
        }

        if ($approverEmail) {
            Mail::to($approverEmail)->queue(
                new \App\Mail\TicketRejectedByItDeptManagerMail($ticket, $validated['remark'], $rejectedBy, $ticket->approvalUser->name ?? null)
            );
        }

        return back()->with('status', 'Ticket rejected by IT Department Manager.');
    }

    // IT Manager rejects ticket
    public function itManagerReject(Request $request, Ticket $ticket): RedirectResponse
    {
        // Check if user has IT Manager role
        if (!$request->user()->hasRole('it_manager')) {
            abort(403);
        }

        if (!in_array($ticket->status, ['it_dept_approved', 'it_reopened', 'dept_reopened', 'requester_reopened', 'it_completed'])) {
            return back()->withErrors(['error' => 'This ticket cannot be rejected at this stage.']);
        }

        $validated = $request->validate([
            'remark' => ['required', 'string', 'max:2000'],
        ]);

        $from = $ticket->status;

        // Update status to it_manager_rejected
        $ticket->update(['status' => 'it_manager_rejected']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_manager_rejected', $validated['remark']);

        // Notify requester, approval user, and IT Department Managers
        $ticket->loadMissing(['requester', 'approvalUser']);
        $rejectedBy = 'IT Manager: ' . ($request->user()->name ?? 'Manager');

        if ($ticket->requester && $ticket->requester->email) {
            Mail::to($ticket->requester->email)->queue(
                new \App\Mail\TicketRejectedByItManagerMail($ticket, $validated['remark'], $rejectedBy, $ticket->requester->name ?? null)
            );
        }

        if ($ticket->approvalUser && $ticket->approvalUser->email) {
            Mail::to($ticket->approvalUser->email)->queue(
                new \App\Mail\TicketRejectedByItManagerMail($ticket, $validated['remark'], $rejectedBy, $ticket->approvalUser->name ?? null)
            );
        }

        $itDeptManagers = User::whereHas('roles', function ($query) {
            $query->where('name', 'it-dept-manager');
        })->get();

        foreach ($itDeptManagers as $itDeptManager) {
            if ($itDeptManager->email) {
                Mail::to($itDeptManager->email)->queue(
                    new \App\Mail\TicketRejectedByItManagerMail($ticket, $validated['remark'], $rejectedBy, $itDeptManager->name ?? null)
                );
            }
        }

        return back()->with('status', 'Ticket rejected by IT Manager.');
    }

    public function assignToItMember(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!in_array($ticket->status, ['it_dept_approved', 'dept_approved', 'approved', 'it_reopened', 'dept_reopened', 'requester_reopened', 'it_dept_reopened_completion'], true)) {
            return back()->withErrors(['it_member_id' => 'This ticket is not ready for IT assignment yet.']);
        }

        $from = $ticket->status;

        $validated = $request->validate([
            'it_member_id' => ['required', 'integer', 'exists:users,id'],
            'it_due_at' => ['required', 'date'],
            'it_instructions' => ['nullable', 'string', 'max:2000'],
        ]);

        // Check if completion date is past the ticket due date
        if ($ticket->needed_by && $validated['it_due_at']) {
            $completionDate = \Carbon\Carbon::parse($validated['it_due_at']);
            $dueDate = \Carbon\Carbon::parse($ticket->needed_by)->endOfDay();
            
            if ($completionDate->greaterThan($dueDate)) {
                return back()->withErrors(['it_due_at' => 'IT completion date cannot be after the ticket due date (' . $ticket->needed_by->format('F j, Y') . ').']);
            }
        }

        $isItMember = User::query()
            ->whereKey($validated['it_member_id'])
            ->whereHas('role', fn($q) => $q->where('name', 'it_member'))
            ->exists();

        if (!$isItMember) {
            return back()->withErrors(['it_member_id' => 'Please select a valid IT member.']);
        }

        $ticket->update([
            'it_member_id' => $validated['it_member_id'],
            'it_due_at' => $validated['it_due_at'],
            'it_instructions' => $validated['it_instructions'] ?? null,
            'status' => 'it_assigned',
        ]);

        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_assigned');

        // Send email to IT member
        $itMember = User::find($validated['it_member_id']);
        if ($itMember && $itMember->email) {
            \Mail::to($itMember->email)->queue(new \App\Mail\TicketAssignedToItMemberMail($ticket->fresh(['itMember', 'requester'])));
        }

        return back()->with('status', 'Ticket assigned to IT member.');
    }

    public function startWork(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->it_member_id !== $request->user()->id) {
            abort(403);
        }

        if (!in_array($ticket->status, ['it_assigned', 'it_reopened'], true)) {
            return back()->withErrors(['status' => 'This ticket cannot be started yet.']);
        }

        $from = $ticket->status;

        $ticket->update(['status' => 'it_in_progress']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_in_progress');

        return back()->with('status', 'Work started.');
    }

    public function markCompleted(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->it_member_id !== $request->user()->id) {
            abort(403);
        }

        if (!in_array($ticket->status, ['it_assigned', 'it_in_progress'], true)) {
            return back()->withErrors(['status' => 'This ticket cannot be completed yet.']);
        }

        $from = $ticket->status;

        $ticket->update(['status' => 'it_completed']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_completed');

        // Send email to IT Manager
        $itManager = User::whereHas('role', fn($q) => $q->where('name', 'it_manager'))->first();
        if ($itManager && $itManager->email) {
            \Mail::to($itManager->email)->queue(new \App\Mail\TicketCompletedByItMemberMail($ticket->fresh(['itMember', 'requester'])));
        }

        return back()->with('status', 'Marked as completed.');
    }

    public function itManagerConfirm(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->status !== 'it_completed') {
            return back()->withErrors(['status' => 'Only completed tickets can be confirmed.']);
        }

        $from = $ticket->status;

        $ticket->update(['status' => 'it_mgr_confirmed']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_mgr_confirmed', 'Confirmed by IT Manager');

        // Notify IT Department Manager to confirm completion
        $ticket->loadMissing(['requester', 'itMember', 'approvalUser']);
        $itDeptManagers = User::whereHas('roles', fn($q) => $q->where('name', 'it-dept-manager'))->get();
        
        foreach ($itDeptManagers as $itDeptManager) {
            if ($itDeptManager->email) {
                Mail::to($itDeptManager->email)->queue(new \App\Mail\ItManagerConfirmedNotifyItDeptManagerMail($ticket));
            }
        }

        return back()->with('status', 'Completion confirmed and sent to IT Department Manager.');
    }

    // IT Department Manager confirms completed job
    public function itDeptManagerConfirmCompletion(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!$request->user()->hasRole('it-dept-manager')) {
            abort(403);
        }

        if ($ticket->status !== 'it_mgr_confirmed') {
            return back()->withErrors(['error' => 'This ticket is not awaiting IT Department Manager confirmation.']);
        }

        $from = $ticket->status;

        $ticket->update(['status' => 'it_dept_confirmed_completion']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_dept_confirmed_completion', 'Completion confirmed by IT Dept Manager');

        // Notify requester and approval user that job is confirmed (no further confirmation required)
        $ticket->loadMissing(['approvalUser', 'requester', 'itMember']);
        if ($ticket->requester && $ticket->requester->email) {
            Mail::to($ticket->requester->email)->queue(new \App\Mail\CompletionConfirmationMail($ticket, $request->user()));
        }
        if ($ticket->approvalUser && $ticket->approvalUser->email) {
            Mail::to($ticket->approvalUser->email)->queue(new \App\Mail\CompletionConfirmedNotifyApproverMail($ticket, $request->user()));
        }

        return back()->with('status', 'Job completion confirmed. Requester and manager have been notified.');
    }

    // IT Department Manager reopens completed job
    public function itDeptManagerReopenCompletion(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!$request->user()->hasRole('it-dept-manager')) {
            abort(403);
        }

        if ($ticket->status !== 'it_mgr_confirmed') {
            return back()->withErrors(['error' => 'This ticket is not awaiting IT Department Manager confirmation.']);
        }

        $validated = $request->validate([
            'remark' => ['required', 'string', 'max:2000'],
        ]);

        $from = $ticket->status;

        $ticket->update(['status' => 'it_dept_reopened_completion']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_dept_reopened_completion', $validated['remark']);

        // Notify IT Manager to reassign
        $itManager = User::whereHas('role', fn($q) => $q->where('name', 'it_manager'))->first();
        if ($itManager && $itManager->email) {
            $ticket->loadMissing(['requester', 'itMember', 'approvalUser']);
            Mail::to($itManager->email)->queue(new \App\Mail\ItDeptManagerReopenedJobMail($ticket));
        }

        return back()->with('status', 'Job reopened and sent back to IT Manager for reassignment.');
    }

    public function itManagerReopen(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->status !== 'it_completed') {
            return back()->withErrors(['status' => 'Only completed tickets can be reopened by IT Manager.']);
        }

        $validated = $request->validate([
            'remark' => ['required', 'string', 'max:2000'],
        ]);

        $from = $ticket->status;

        $ticket->update(['status' => 'it_reopened']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'it_reopened', $validated['remark']);

        // Notify the assigned IT member that the ticket was reopened
        $ticket->loadMissing(['itMember', 'requester']);
        if ($ticket->itMember && $ticket->itMember->email) {
            Mail::to($ticket->itMember->email)->queue(
                new TicketReopenedByItManagerMail($ticket, $validated['remark'])
            );
            Log::info('Queued reopen email for IT member', [
                'ticket_id' => $ticket->id,
                'recipient' => $ticket->itMember->email,
            ]);
        }

        return back()->with('status', 'Ticket reopened and returned to IT.');
    }

    public function deptManagerConfirm(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->approval_user_id !== $request->user()->id) {
            abort(403);
        }

        if ($ticket->status !== 'it_mgr_confirmed') {
            return back()->withErrors(['status' => 'This ticket is not ready for department confirmation yet.']);
        }

        $from = $ticket->status;

        $to = $ticket->requester_id === $ticket->approval_user_id
            ? 'requester_confirmed'
            : 'dept_confirmed';

        $ticket->update(['status' => $to]);
        $this->recordStatusChange(
            $ticket,
            $request->user()->id,
            $from,
            $to,
            $to === 'requester_confirmed' ? 'Auto requester confirm (same user).' : null
        );

        // Notify requester to review/confirm when department manager confirms
        if ($to === 'dept_confirmed') {
            $ticket->loadMissing(['requester', 'approvalUser', 'itMember']);
            if ($ticket->requester && $ticket->requester->email) {
                Mail::to($ticket->requester->email)->queue(
                    new TicketDeptConfirmedNotifyRequesterMail($ticket)
                );
            }
        }

        return back()->with('status', $to === 'requester_confirmed' ? 'Department + requester confirmed.' : 'Department confirmed.');
    }

    public function deptManagerReopen(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->approval_user_id !== $request->user()->id) {
            abort(403);
        }

        if (!in_array($ticket->status, ['it_mgr_confirmed', 'it_dept_confirmed_completion'])) {
            return back()->withErrors(['status' => 'Only tickets awaiting department confirmation can be reopened.']);
        }

        $validated = $request->validate([
            'remark' => ['required', 'string', 'max:2000'],
        ]);

        $from = $ticket->status;

        $ticket->update(['status' => 'dept_reopened']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'dept_reopened', $validated['remark']);

        // Notify IT Manager that the ticket was reopened and needs reassignment
        $itManager = User::whereHas('role', fn($q) => $q->where('name', 'it_manager'))->first();
        if ($itManager && $itManager->email) {
            Mail::to($itManager->email)->queue(new TicketReopenedByDeptManagerMail($ticket->loadMissing(['requester', 'itMember']), $validated['remark']));
        }

        return back()->with('status', 'Ticket reopened and returned to IT.');
    }

    public function requesterConfirm(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->requester_id !== $request->user()->id) {
            abort(403);
        }

        if (!in_array($ticket->status, ['dept_confirmed', 'it_dept_confirmed_completion'])) {
            return back()->withErrors(['status' => 'This ticket is not ready for requester confirmation yet.']);
        }

        $from = $ticket->status;

        $ticket->update(['status' => 'requester_confirmed']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'requester_confirmed');

        // Send final confirmation email to actual requester if email exists
        $ticket->loadMissing(['requester', 'itMember']);
        if ($ticket->actual_requester_email) {
            Mail::to($ticket->actual_requester_email)->later(now(), new \App\Mail\RequesterFinalConfirmationMail($ticket));
        }

        return back()->with('status', 'Job marked as completed.');
    }

    public function requesterReopen(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->requester_id !== $request->user()->id) {
            abort(403);
        }

        if ($ticket->status !== 'dept_confirmed') {
            return back()->withErrors(['status' => 'This ticket cannot be reopened at this stage.']);
        }

        $validated = $request->validate([
            'remark' => ['required', 'string', 'max:2000'],
        ]);

        $from = $ticket->status;

        $ticket->update([
            'status' => 'requester_reopened',
        ]);

        $this->recordStatusChange($ticket, $request->user()->id, $from, 'requester_reopened', $validated['remark']);

        // Notify IT Manager that the requester reopened the ticket
        $itManager = User::whereHas('role', fn($q) => $q->where('name', 'it_manager'))->first();
        if ($itManager && $itManager->email) {
            $ticket->loadMissing(['requester', 'itMember', 'approvalUser']);
            Mail::to($itManager->email)->queue(
                new TicketReopenedByRequesterMail($ticket, $validated['remark'])
            );
        }

        return back()->with('status', 'Ticket reopened and sent back to IT.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'string', 'in:Hardware,Software,Access,Network,Email,Other'],
            'priority' => ['required', 'string', 'in:Low,Normal,High'],
            'needed_by' => ['required', 'date'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'approval_user_id' => ['required', 'integer', 'exists:users,id'],
            'actual_requester_name' => ['nullable', 'string', 'max:255'],
            'actual_requester_email' => ['nullable', 'email', 'max:255'],
            'attachments.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,txt'],
        ]);

        $requester = $request->user()?->loadMissing('role');
        $requesterRole = $requester?->role?->name;

        $approvalUserId = (int) $validated['approval_user_id'];

        $approvalUserIsAllowed = match ($requesterRole) {
            // Dept manager can approve their own ticket.
            'dept_manager' => $approvalUserId === (int) $requester->id,

            // Section manager can approve their own ticket OR send to a dept manager.
            'section_manager' => ($approvalUserId === (int) $requester->id)
            || User::query()
                ->whereKey($approvalUserId)
                ->whereHas('role', fn($q) => $q->where('name', 'dept_manager'))
                ->exists(),

            // Others can choose dept_manager or section_manager.
            default => User::query()
                ->whereKey($approvalUserId)
                ->whereHas('role', fn($q) => $q->whereIn('name', ['dept_manager', 'section_manager']))
                ->exists(),
        };

        if (!$approvalUserIsAllowed) {
            return back()
                ->withInput()
                ->withErrors(['approval_user_id' => 'Please select a valid approval person.']);
        }

        $approvalUser = User::query()->find($approvalUserId);
        if (!$approvalUser?->email) {
            return back()
                ->withInput()
                ->withErrors(['approval_user_id' => 'Selected approval person does not have an email address.']);
        }

        $ticket = Ticket::create([
            'requester_id' => $request->user()->id,
            'approval_user_id' => $validated['approval_user_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'needed_by' => $validated['needed_by'],
            'section_id' => $validated['section_id'],
            'actual_requester_name' => $validated['actual_requester_name'] ?? null,
            'actual_requester_email' => $validated['actual_requester_email'] ?? null,
            'status' => 'pending',
        ]);

        $this->recordStatusChange($ticket, $request->user()->id, null, 'pending');

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $storedName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('ticket_attachments', $storedName, 'public');

                $ticket->attachments()->create([
                    'original_filename' => $originalName,
                    'stored_filename' => $storedName,
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        try {
            $ticket->loadMissing(['requester:id,name,email', 'approvalUser:id,name,email']);

            $approveUrl = TicketApprovalRequestMail::buildApproveUrl($ticket);
            $rejectUrl = TicketApprovalRequestMail::buildRejectUrl($ticket);
            $cutoff = TicketApprovalRequestMail::approvalCutoff($ticket);

            Mail::to($ticket->approvalUser->email)->queue(
                new TicketApprovalRequestMail($ticket, $approveUrl, $rejectUrl, $cutoff)
            );
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('tickets.index')
                ->with('status', 'Ticket submitted successfully, but the approval email could not be sent.');
        }

        return redirect()->route('tickets.index')->with('status', 'Ticket submitted successfully.');
    }

    public function downloadAttachment(Request $request, TicketAttachment $attachment)
    {
        $ticket = $attachment->ticket;
        $user = $request->user();

        // Check authorization
        $allowed = $user->role?->name === 'it_manager'
            || $ticket->requester_id === $user->id
            || $ticket->approval_user_id === $user->id
            || $ticket->it_member_id === $user->id;

        if (!$allowed) {
            abort(403);
        }

        return response()->download(
            storage_path('app/public/' . $attachment->file_path),
            $attachment->original_filename
        );
    }
}