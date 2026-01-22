@extends('emails.layout', ['headerColor' => '#2563eb', 'headerColorDark' => '#1d4ed8', 'accentColor' => '#2563eb'])

@section('header')
    <h1>✅ IT Manager Confirmed</h1>
    <p>Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>{{ $ticket->approvalUser->name ?? 'Manager' }}</strong>,</p>

    <p class="message">
        The IT Manager has confirmed completion of the ticket below. Please review and either confirm or reopen if more work is needed.
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
            <span class="info-label">Requester:</span>
            <span class="info-value">{{ $ticket->requester->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Completed By:</span>
            <span class="info-value">{{ $ticket->itMember->name ?? 'IT Member' }}</span>
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
        <p><strong>Next step:</strong> Please log in and confirm or reopen this ticket.</p>
    </div>

    <div class="button-container">
        <a href="{{ route('dashboard.manager') }}" class="button button-primary">Go to Manager Dashboard</a>
        <a href="{{ route('tickets.show', $ticket) }}" class="button button-secondary">View Ticket #{{ $ticket->id }}</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        You can confirm or reopen this ticket from your dashboard.
    </p>
@endsection
