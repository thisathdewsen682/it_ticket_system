@extends('emails.layout')

@section('header')
    <h1>🔔 Approval Required</h1>
    <p>New Ticket Awaiting Your Decision</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>{{ $ticket->approvalUser->name ?? 'Manager' }}</strong>,</p>

    <p class="message">
        A new IT Job has been submitted and requires your approval before work can begin. Please review the details below and make your decision.
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

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value" style="color: #dc2626; font-weight: 600;">
                {{ $ticket->needed_by->timezone('Asia/Colombo')->format('F j, Y') }}
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
        <p><strong>⏰ Approval Deadline:</strong> {{ $approvalCutoff->timezone('Asia/Colombo')->format('F j, Y - g:i A') }}</p>
        <p style="margin-top: 8px;">Please make your decision before this deadline to avoid automatic expiration.</p>
    </div>
    @endif

    <div class="button-container">
        <a href="{{ $approveUrl }}" class="button button-success">✓ Approve Job</a>
        <a href="{{ $rejectUrl }}" class="button button-danger">✗ Reject Job</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        You can also review this job by logging into your dashboard:<br>
        <a href="{{ url('/approvals') }}" style="color: #059669; text-decoration: none; font-weight: 600;">Go to Approvals Dashboard</a>
    </p>
@endsection
