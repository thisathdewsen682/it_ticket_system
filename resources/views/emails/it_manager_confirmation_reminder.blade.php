@extends('emails.layout', ['headerColor' => '#f97316', 'headerColorDark' => '#ea580c', 'accentColor' => '#f97316'])

@section('header')
    <h1>⏰ Reminder: Confirmation Required</h1>
    <p>Pending Ticket Confirmation</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>IT Manager</strong>,</p>

    <p class="message">
        This is a <strong>reminder</strong> that the following job has been completed by the IT member and is awaiting your confirmation. Please review and take action soon.
    </p>

    <div class="info-card">
        <h3>📋 Job Details</h3>
        
        <div class="info-row">
            <span class="info-label">Job ID:</span>
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
            <span class="info-label">Completed At:</span>
            <span class="info-value">{{ $ticket->updated_at->timezone('Asia/Colombo')->format('F j, Y - H:i') }}</span>
        </div>

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value" style="color: #dc2626; font-weight: 600;">
                {{ $ticket->needed_by->timezone('Asia/Colombo')->format('F j, Y') }}
                @if(now()->greaterThan($ticket->needed_by))
                    <span class="badge badge-urgent">OVERDUE</span>
                @endif
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
        <p><strong>⚠️ Action Required:</strong> This job is waiting for your confirmation. Please review and confirm or reopen as needed.</p>
    </div>

    <div class="button-container">
        <a href="{{ route('dashboard.it_manager', ['tab' => 'pending_confirmation']) }}" class="button button-primary">Review & Confirm</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Access your IT Manager dashboard to take action on this job.
    </p>
@endsection
