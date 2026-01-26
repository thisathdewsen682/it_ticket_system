@extends('emails.layout', ['headerColor' => '#10b981', 'headerColorDark' => '#059669', 'accentColor' => '#10b981'])

@section('header')
    <h1>✅ Your Ticket has been Approved</h1>
    <p>Please Confirm or Reopen</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>{{ $ticket->requester->name }}</strong>,</p>

    <p class="message">
        Good news! Your ticket <strong>#{{ $ticket->id }}</strong> has been <strong>approved</strong> by the department manager. The IT team has completed the work and the solution has been verified.
    </p>

    <p class="message">
        Please <strong>confirm that the issue has been resolved</strong>, or reopen the ticket if additional work is needed.
    </p>

    <div class="info-card">
        <h3>📋 Ticket Details</h3>
        
        <div class="info-row">
            <span class="info-label">Ticket ID:</span>
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
            <span class="info-label">Assigned IT Member:</span>
            <span class="info-value">{{ $ticket->itMember->name ?? 'N/A' }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Approved By:</span>
            <span class="info-value">{{ $ticket->approvalUser->name ?? 'N/A' }}</span>
        </div>

        @if($ticket->description)
            <div class="info-row">
                <span class="info-label">Your Issue:</span>
                <span class="info-value">{{ Str::limit($ticket->description, 150) }}</span>
            </div>
        @endif
    </div>

    <div class="alert-box">
        <strong>⏰ Action Required:</strong> <p>Please log in to the IT Job Management System to confirm this job is resolved or reopen it if more work is needed.</p>
    </div>

    <div class="instructions-box">
        <strong>What to Do Next:</strong>
        <p>1. Log in to the IT Ticket System
2. Review the work completed on ticket #{{ $ticket->id }}
3. Click "CONFIRM" if the issue is fully resolved
4. Click "REOPEN" if more work is needed
5. Add a remark (optional) explaining your decision</p>
    </div>

    <p class="message">
        If you have any questions or concerns about the work completed, please reply to this email or contact the IT department directly.
    </p>
@endsection
