@extends('emails.layout', ['headerColor' => '#16a34a', 'headerColorDark' => '#15803d', 'accentColor' => '#16a34a'])

@section('header')
    <h1>✅ Department Confirmed</h1>
    <p>Review Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>{{ $ticket->requester->name ?? 'Requester' }}</strong>,</p>

    <p class="message">
        The department manager has confirmed the work on this ticket. Please review and either confirm completion or reopen if more work is needed.
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
            <span class="info-label">Department Manager:</span>
            <span class="info-value">{{ $ticket->approvalUser->name ?? 'Manager' }}</span>
        </div>
        @if($ticket->itMember)
        <div class="info-row">
            <span class="info-label">IT Member:</span>
            <span class="info-value">{{ $ticket->itMember->name }}</span>
        </div>
        @endif
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

    <div class="alert-box">
        <p><strong>Next step:</strong> Please log in to confirm completion or reopen the ticket if you are not satisfied.</p>
    </div>

    <div class="button-container">
        <a href="{{ route('tickets.index') }}" class="button button-primary">Go to My Tickets</a>
        <a href="{{ route('tickets.show', $ticket) }}" class="button button-secondary">View Ticket #{{ $ticket->id }}</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        You can confirm or reopen this ticket from your tickets page.
    </p>
@endsection
