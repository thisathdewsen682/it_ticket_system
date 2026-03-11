@extends('emails.layout', ['headerColor' => '#10b981', 'headerColorDark' => '#059669', 'accentColor' => '#10b981'])

@section('header')
    <h1>✅ Job Completed</h1>
    <p>Job Confirmed Complete - Job #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>{{ $ticket->approvalUser->name ?? 'Approver' }}</strong>,</p>

    <p class="message">
        The IT Department Manager has confirmed completion of the job below. If you experience any issues or problems, please contact the IT team for assistance.
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
            <span class="info-label">Completed By:</span>
            <span class="info-value">{{ $ticket->itMember->name ?? 'IT Member' }}</span>
        </div>
        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value">{{ $ticket->needed_by->timezone('Asia/Colombo')->format('F j, Y') }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Completed On:</span>
            <span class="info-value">{{ now()->timezone('Asia/Colombo')->format('F j, Y g:i A') }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        <strong>Note:</strong> No further action is required from you. Only the requester can confirm or reopen this job.
    </p>
@endsection