@extends('emails.layout', ['headerColor' => '#ea580c', 'headerColorDark' => '#c2410c', 'accentColor' => '#ea580c'])

@section('header')
    <h1>🔄 Job Reopened - Reassignment Required</h1>
    <p>IT Department Manager Reopened - Job #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>IT Manager</strong>,</p>

    <p class="message">
        The IT Department Manager has reopened the following ticket. The work needs to be reviewed and the ticket should be reassigned to an IT member for further action.
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
            <span class="info-label">Requester:</span>
            <span class="info-value">{{ $ticket->requester->name ?? 'N/A' }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Previous IT Member:</span>
            <span class="info-value">{{ $ticket->itMember->name ?? 'N/A' }}</span>
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

    @if($ticket->statusHistories->where('to_status', 'it_dept_reopened_completion')->last())
    <div class="alert-box" style="background-color: #fee2e2; border-left-color: #dc2626;">
        <p><strong>🔄 Reopen Reason:</strong></p>
        <p style="margin-top: 8px; color: #991b1b;">
            {{ $ticket->statusHistories->where('to_status', 'it_dept_reopened_completion')->last()->remark ?? 'No reason provided' }}
        </p>
    </div>
    @endif

    <div class="alert-box">
        <p><strong>⚠️ Action Required:</strong> Please review this ticket and reassign it to an IT member. The previous work has been flagged for revision.</p>
    </div>

    <div class="button-container">
        <a href="{{ route('dashboard.it_manager', ['tab' => 'reopened']) }}" class="button button-primary">Reassign Job</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Log in to your IT Manager dashboard to reassign this job.
    </p>
@endsection
