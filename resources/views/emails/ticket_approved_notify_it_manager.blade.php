@extends('emails.layout', ['headerColor' => '#059669', 'headerColorDark' => '#047857', 'accentColor' => '#059669'])

@section('header')
    <h1>✅ Ticket Approved</h1>
    <p>Assignment Required - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>IT Manager</strong>,</p>

    <p class="message">
        A ticket has been approved by the department manager and is now ready for IT member assignment. Please review and assign an IT member to handle this request.
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
            <span class="info-label">Approved By:</span>
            <span class="info-value">{{ $ticket->approvalUser->name ?? 'N/A' }}</span>
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
        <p><strong>⚠️ Action Required:</strong> Please assign an IT member to this ticket to begin work.</p>
    </div>

    <div class="button-container">
        <a href="{{ url('/dashboard/it-manager?tab=approved') }}" class="button button-primary">Assign IT Member</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Log in to your IT Manager dashboard to assign this ticket to an available IT member.
    </p>
@endsection
