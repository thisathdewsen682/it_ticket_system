@extends('emails.layout', ['headerColor' => '#ef4444', 'headerColorDark' => '#dc2626', 'accentColor' => '#ef4444'])

@section('header')
    <h1>❌ Your Ticket was Rejected</h1>
    <p>Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>{{ $ticket->requester->name ?? 'Requester' }}</strong>,</p>

    <p class="message">
        Your ticket <strong>#{{ $ticket->id }}</strong> has been <strong>rejected</strong> by the department/section manager.
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
            <span class="info-label">Rejected By:</span>
            <span class="info-value">{{ $ticket->approvalUser->name ?? 'Manager' }}</span>
        </div>
    </div>

    <div class="alert-box">
        <strong>Rejection Reason:</strong>
        <p>{{ $remark }}</p>
    </div>

    <div class="button-container">
        <a href="{{ url('/tickets') }}" class="button button-primary">View Your Tickets</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        If you need to submit a new request, please create a new ticket.
    </p>
@endsection
