@extends('emails.layout', ['headerColor' => '#8b5cf6', 'headerColorDark' => '#7c3aed', 'accentColor' => '#8b5cf6'])

@section('header')
    <h1>✅ ACTION REQUIRED: Ticket Confirmation Needed</h1>
    <p>Pending Approver Confirmation</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>{{ $approverName }}</strong>,</p>

    <p class="message">
        This is a <strong>reminder</strong> that the following {{ count($tickets) }} ticket(s) have been completed by the IT Manager and are awaiting your confirmation. Please review and take action soon.
    </p>

    <div class="alert-box">
        <strong>⏰ Action Required:</strong> <p>Please confirm or reopen these tickets as needed.</p>
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
                <span class="info-label">Requester:</span>
                <span class="info-value">{{ $ticket['requester_name'] ?? 'N/A' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Completed By:</span>
                <span class="info-value">{{ $ticket['it_member_name'] ?? 'N/A' }}</span>
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
        </div>
    @endforeach

    <p class="message">
        Once you confirm these tickets, the requesters will be notified that their issues have been resolved.
    </p>

    <div class="instructions-box">
        <strong>Next Steps:</strong>
        <p>1. Review each ticket completion
2. Confirm if the work is acceptable
3. Reopen the ticket if more work is needed
4. Log in to the IT Ticket System to take action</p>
    </div>
@endsection
