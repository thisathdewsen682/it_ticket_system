<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
        if (!in_array($action, ['approve', 'reject'], true)) {
            abort(404);
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
            $this->recordStatusChange($ticket, null, $from, 'dept_approved', 'Approved via email link.');

            return view('tickets.approval_link_result', [
                'ticket' => $ticket,
                'status' => 'approved',
                'action' => $action,
                'message' => 'Ticket approved successfully.',
            ]);
        }

        $ticket->update(['status' => 'dept_rejected']);
        $this->recordStatusChange($ticket, null, $from, 'dept_rejected', 'Rejected via email link.');

        return view('tickets.approval_link_result', [
            'ticket' => $ticket,
            'status' => 'rejected',
            'action' => $action,
            'message' => 'Ticket rejected successfully.',
        ]);
    }
}
