<?php

namespace App\Http\Controllers;

use App\Mail\TicketApprovedNotifyItDeptManagerMail;
use App\Mail\TicketApprovedNotifyRequesterMail;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TicketApprovalLinkController extends Controller
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

    public function __invoke(Request $request, Ticket $ticket, string $action)
    {
        // Load attachments relationship
        $ticket->load('attachments');

        if (!in_array($action, ['approve', 'reject'], true)) {
            abort(404);
        }

        // Verify the logged-in user is the approval person
        if ($ticket->approval_user_id !== $request->user()->id) {
            Auth::logout();

            return redirect()
                ->guest(route('login'))
                ->with('status', 'Please sign in as the approver to approve/reject this ticket.');
        }

        // Extra defense-in-depth: signed URL already enforces expiry, but this ensures
        // the business rule holds even if needed_by is changed.
        if ($ticket->needed_by) {
            $approvalCutoff = Carbon::parse($ticket->needed_by)->endOfDay();
            if (now()->greaterThan($approvalCutoff)) {
                return response()->view('tickets.approval_link_result', [
                    'ticket' => $ticket,
                    'status' => 'expired',
                    'action' => $action,
                    'message' => 'This approval link has expired (due date passed).',
                ], 403);
            }
        }

        if ($ticket->status !== 'pending') {
            return view('tickets.approval_link_result', [
                'ticket' => $ticket,
                'status' => 'already_decided',
                'action' => $action,
                'message' => 'This ticket is no longer pending approval.',
            ]);
        }

        $from = $ticket->status;

        if ($action === 'approve') {
            $ticket->update(['status' => 'dept_approved']);
            $this->recordStatusChange($ticket, $request->user()->id, $from, 'dept_approved', 'Approved via email link.');

            // Notify IT Dept Manager (same as dashboard approval path)
            $itDeptManager = User::query()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'it-dept-manager');
                })
                ->orWhereHas('role', function ($query) {
                    $query->where('name', 'it-dept-manager');
                })
                ->first();

            if ($itDeptManager && $itDeptManager->email) {
                $ticket->loadMissing(['requester', 'approvalUser']);
                Mail::to($itDeptManager->email)->queue(new TicketApprovedNotifyItDeptManagerMail($ticket));
            }

            // Notify Requester - Immediate email that ticket has been approved
            if ($ticket->requester && $ticket->requester->email) {
                $ticket->loadMissing(['requester', 'approvalUser', 'itMember']);
                Mail::to($ticket->requester->email)->queue(new TicketApprovedNotifyRequesterMail($ticket));
            }

            return view('tickets.approval_link_result', [
                'ticket' => $ticket,
                'status' => 'approved',
                'action' => $action,
                'message' => 'Ticket approved successfully.',
            ]);
        }

        $ticket->update(['status' => 'dept_rejected']);
        $this->recordStatusChange($ticket, $request->user()->id, $from, 'dept_rejected', 'Rejected via email link.');

        $ticket->loadMissing(['requester', 'approvalUser']);
        if ($ticket->requester && $ticket->requester->email) {
            $rejectedBy = 'Department/Section Manager: ' . ($request->user()->name ?? 'Manager');
            Mail::to($ticket->requester->email)->queue(
                new \App\Mail\TicketRejectedByDeptManagerMail($ticket, 'Rejected via email link.', $rejectedBy, $ticket->requester->name ?? null)
            );
        }

        return view('tickets.approval_link_result', [
            'ticket' => $ticket,
            'status' => 'rejected',
            'action' => $action,
            'message' => 'Ticket rejected successfully.',
        ]);
    }
}
