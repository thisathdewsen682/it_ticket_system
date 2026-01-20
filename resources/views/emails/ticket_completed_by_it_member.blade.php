@extends('emails.layout', ['headerColor' => '#7c3aed', 'headerColorDark' => '#6d28d9', 'accentColor' => '#7c3aed'])

@section('header')
    <h1>✅ Ticket Completed</h1>
    <p>Confirmation Required - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>IT Manager</strong>,</p>

    <p class="message">
        An IT member has marked the following ticket as completed. Please review the work and confirm or reopen the ticket if additional work is needed.
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
            <span class="info-label">Completed By:</span>
            <span class="info-value">{{ $ticket->itMember->name ?? 'N/A' }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Requester:</span>
            <span class="info-value">{{ $ticket->requester->name ?? 'N/A' }}</span>
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

    <div class="alert-box">
        <p><strong>⚠️ Action Required:</strong> Please log in to your dashboard to confirm the completion or reopen the ticket if more work is needed.</p>
    </div>

    <div class="button-container">
        <a href="{{ url('/dashboard/it-manager?tab=pending_confirmation') }}" class="button button-primary">Go to Dashboard</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        You can confirm or reopen this ticket from your IT Manager dashboard.
    </p>
@endsection
