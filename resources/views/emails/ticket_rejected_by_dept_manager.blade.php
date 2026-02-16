@extends('emails.layout', ['headerColor' => '#ef4444', 'headerColorDark' => '#dc2626', 'accentColor' => '#ef4444'])

@section('header')
    <h1>❌ Job Rejected</h1>
    <p>Rejected by Department/Section Manager - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>{{ $recipientName ?? ($ticket->requester->name ?? 'Requester') }}</strong>,</p>

    <p class="message">
        Your job <strong>#{{ $ticket->id }}</strong> has been <strong>rejected</strong> by <strong>{{ $rejectedBy }}</strong>.
        Resubmitting this job will require you to create a new job with the necessary adjustments based on the rejection reason provided below.
    </p>

    <div class="info-card">
        <h3>📋 Job Details</h3>

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
            <span class="info-value">{{ $rejectedBy }}</span>
        </div>
    </div>

    <div class="alert-box">
        <strong>Rejection Reason:</strong>
        <p>{{ $remark }}</p>
    </div>

    <div class="button-container">
        <a href="{{ url('/tickets') }}" class="button button-primary">View Your Jobs</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        If you need to submit a new request, please create a new ticket.
    </p>
@endsection
