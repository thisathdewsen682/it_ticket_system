@extends('emails.layout', ['headerColor' => '#f97316', 'headerColorDark' => '#ea580c', 'accentColor' => '#f97316'])

@section('header')
    <h1>⏰ Reminder: Approval Required</h1>
    <p>Pending Job Needs Your Attention</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>{{ $ticket->approvalUser->name }}</strong>,</p>

    <p class="message">
        This is a <strong>friendly reminder</strong> that the following ticket is still pending your approval. Please review and make your decision at your earliest convenience.
    </p>

    <div class="info-card">
        <h3>📋 Job Details</h3>
        
        <div class="info-row">
            <span class="info-label">Job ID:</span>
            <span class="info-value"><strong>#{{ $ticket->id }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">Title:</span>
            <span class="info-value">{{ $ticket->title }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Category:</span>
            <span class="info-value">{{ $ticket->category }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Priority:</span>
            <span class="info-value">
                <span class="badge badge-{{ strtolower($ticket->priority) }}">{{ $ticket->priority }}</span>
            </span>
        </div>

        <div class="info-row">
            <span class="info-label">Requester:</span>
            <span class="info-value">{{ $ticket->requester->name ?? 'N/A' }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Created:</span>
            <span class="info-value">{{ $ticket->created_at->format('F j, Y - H:i') }}</span>
        </div>

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value" style="color: #dc2626; font-weight: 600;">
                {{ $ticket->needed_by->format('F j, Y') }}
            </span>
        </div>
        @endif

        @if($ticket->description)
        <div class="info-row" style="display: block; border-bottom: none; padding-top: 15px;">
            <span class="info-label">Description:</span>
            <div class="description-box">
                <p>{{ $ticket->description }}</p>
            </div>
        </div>
        @endif
    </div>

    @if($approvalCutoff)
    <div class="alert-box">
        <p><strong>⏰ Approval Deadline:</strong> {{ $approvalCutoff->format('F j, Y - 11:59 PM') }}</p>
        <p style="margin-top: 8px;">Please take action soon to avoid missing the deadline.</p>
    </div>
    @endif

    <div class="button-container">
        <a href="{{ $approveUrl }}" class="button button-success">✓ Approve Ticket</a>
        <a href="{{ $rejectUrl }}" class="button button-danger">✗ Reject Ticket</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Access your approvals dashboard:<br>
        <a href="{{ url('/approvals') }}" style="color: #f97316; text-decoration: none; font-weight: 600;">Go to Approvals Dashboard</a>
    </p>
@endsection
