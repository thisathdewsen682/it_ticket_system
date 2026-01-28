@extends('emails.layout', ['headerColor' => '#10b981', 'headerColorDark' => '#059669', 'accentColor' => '#10b981'])

@section('header')
    <h1>✅ Ticket Completed</h1>
    <p>Confirmation Sent - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>{{ $ticket->requester->name ?? 'Requester' }}</strong>,</p>

    <p class="message">
        Your ticket has been confirmed as completed. If you need any further work on this request, please contact the IT team or reopen the ticket from your dashboard.
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

        @if($ticket->itMember)
        <div class="info-row">
            <span class="info-label">IT Member:</span>
            <span class="info-value">{{ $ticket->itMember->name }}</span>
        </div>
        @endif

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value">{{ $ticket->needed_by->format('F j, Y') }}</span>
        </div>
        @endif
    </div>

    <div class="button-container">
        <a href="{{ url('/tickets') }}" class="button button-primary">View Your Tickets</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Thank you for using the IT Job Management System.
    </p>
@endsection
