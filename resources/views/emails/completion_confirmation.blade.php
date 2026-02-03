@extends('emails.layout', ['headerColor' => '#10b981', 'headerColorDark' => '#059669', 'accentColor' => '#10b981'])

@section('header')
    <h1>✅ Ticket Completed</h1>
    <p>Job Confirmed Complete - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>{{ $ticket->requester->name ?? 'Requester' }}</strong>,</p>

    <p class="message">
        Your IT support ticket has been successfully completed and confirmed by the Department Manager.
        @if($deptManager)
        <strong>{{ $deptManager->name }}</strong> has verified that the work has been completed to satisfaction.
        @endif
    </p>
    
    <p class="message">
        The job is now closed. If you need any further assistance or have concerns about this ticket, please contact your department manager or submit a new ticket.
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
            <span class="info-label">Handled By:</span>
            <span class="info-value">{{ $ticket->itMember->name }} (IT Member)</span>
        </div>
        @endif
        
        @if($deptManager)
        <div class="info-row">
            <span class="info-label">Confirmed By:</span>
            <span class="info-value">{{ $deptManager->name }} ({{ $ticket->approvalUser->role->name ?? 'Department Manager' }})</span>
        </div>
        @endif

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value">{{ $ticket->needed_by->format('F j, Y') }}</span>
        </div>
        @endif
        
        <div class="info-row">
            <span class="info-label">Completed On:</span>
            <span class="info-value">{{ now()->format('F j, Y g:i A') }}</span>
        </div>
    </div>

    <div class="button-container">
        <a href="{{ url('/tickets') }}" class="button button-primary">View Your Tickets</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Thank you for using the IT Job Management System.
    </p>
@endsection
