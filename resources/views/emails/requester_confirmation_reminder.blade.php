@extends('emails.layout', ['headerColor' => '#3b82f6', 'headerColorDark' => '#2563eb', 'accentColor' => '#3b82f6'])

@section('header')
    <h1>📝 REMINDER: Please Confirm Your Tickets</h1>
    <p>Action Needed</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>{{ $requesterName }}</strong>,</p>

    <p class="message">
        This is a <strong>reminder</strong> that the following {{ count($tickets) }} ticket(s) have been <strong>approved and completed</strong> by the department manager. Your confirmation is still pending.
    </p>

    <p class="message">
        Please review the completed work and <strong>confirm the issue is resolved, or reopen if more work is needed</strong>.
    </p>

    <div class="alert-box">
        <strong>⏰ Action Required:</strong> <p>Please confirm or reopen these jobs in the IT Job Management System.</p>
    </div>

    @foreach($tickets as $index => $ticket)
        <div class="info-card">
            <h3>📋 Ticket #{{ $ticket['id'] }} - {{ $ticket['title'] }}</h3>
            
            <div class="info-row">
                <span class="info-label">Ticket ID:</span>
                <span class="info-value"><strong>#{{ $ticket['id'] }}</strong></span>
            </div>

            <div class="info-row">
                <span class="info-label">Title:</span>
                <span class="info-value">{{ $ticket['title'] }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Category:</span>
                <span class="info-value">{{ $ticket['category'] ?? 'N/A' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Priority:</span>
                <span class="info-value">
                    <span class="badge badge-{{ strtolower($ticket['priority'] ?? 'normal') }}">{{ $ticket['priority'] ?? 'Normal' }}</span>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Completed By:</span>
                <span class="info-value">{{ $ticket['it_member_name'] ?? 'N/A' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Approved By:</span>
                <span class="info-value">{{ $ticket['approval_user_name'] ?? 'N/A' }}</span>
            </div>
        </div>
    @endforeach

   <!-- <div class="instructions-box">
        <strong>How to Confirm or Reopen:</strong>
        <p>1. Log in to the IT Ticket System
2. Navigate to your pending tickets
3. For each ticket:
   - Click "CONFIRM" if the work is complete and satisfactory
   - Click "REOPEN" if more work is needed
   - Add a remark explaining your decision (optional)</p>
    </div>-->

    <p class="message">
        Thank you for your prompt attention to these tickets. If you have any questions about the completed work, please contact the IT department directly.
    </p>
@endsection
