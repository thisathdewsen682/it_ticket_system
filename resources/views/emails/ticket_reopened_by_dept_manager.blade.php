@extends('emails.layout', ['headerColor' => '#ea580c', 'headerColorDark' => '#c2410c', 'accentColor' => '#ea580c'])

@section('header')
    <h1>🔁 Ticket Reopened</h1>
    <p>Manager Reopened - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>IT Manager</strong>,</p>

    <p class="message">
        The department/section manager reopened the ticket below. Please review and reassign an IT member if needed.
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
        @if($ticket->itMember)
        <div class="info-row">
            <span class="info-label">Current IT Member:</span>
            <span class="info-value">{{ $ticket->itMember->name }}</span>
        </div>
        @endif
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

    <div class="instructions-box">
        <strong>Manager's Reopen Note:</strong>
        <p>{{ $remark }}</p>
    </div>

    <div class="button-container">
        <a href="{{ route('dashboard.it_manager') }}" class="button button-primary">Go to IT Manager Dashboard</a>
        <a href="{{ route('tickets.show', $ticket) }}" class="button button-secondary">View Ticket #{{ $ticket->id }}</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Please reassign or update the ticket as needed.
    </p>
@endsection
