@extends('emails.layout', ['headerColor' => '#7c3aed', 'headerColorDark' => '#6d28d9', 'accentColor' => '#7c3aed'])

@section('header')
    <h1>✅ Job Completion - Confirmation Required</h1>
    <p>IT Department Manager Action Needed - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>IT Department Manager</strong>,</p>

    <p class="message">
        The IT Manager has confirmed that the job has been completed. Please review and confirm the completion, or reopen if further work is needed.
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
            <span class="info-label">Handled By:</span>
            <span class="info-value">{{ $ticket->itMember->name ?? 'N/A' }} (IT Member)</span>
        </div>

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value">{{ $ticket->needed_by->timezone('Asia/Colombo')->format('F j, Y') }}</span>
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
        <p><strong>⚠️ Action Required:</strong> Please review the completed work and either confirm the completion or reopen if the work needs revision.</p>
    </div>

    <div class="button-container">
        <a href="{{ url('/dashboard/unified?role_tab=it-dept-manager&tab=pending_completion') }}" class="button button-primary">Review Completion</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Log in to your IT Department Manager dashboard to confirm or reopen this ticket.
    </p>
@endsection
