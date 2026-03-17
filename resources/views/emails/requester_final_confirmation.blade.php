@extends('emails.layout', ['headerColor' => '#10b981', 'headerColorDark' => '#059669', 'accentColor' => '#10b981'])

@section('header')
    <h1>✅ Job Completed Successfully</h1>
    <p>Final Confirmation - Job #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>{{ $ticket->actual_requester_name ?? 'Job Requestor' }}</strong>,</p>

    <p class="message">
        The IT job that was requested on your behalf has been completed and confirmed. The work has been successfully finished and approved by all parties.
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

        @if($ticket->requester)
        <div class="info-row">
            <span class="info-label">Submitted By:</span>
            <span class="info-value">{{ $ticket->requester->name }}</span>
        </div>
        @endif

        @if($ticket->itMember)
        <div class="info-row">
            <span class="info-label">Handled By:</span>
            <span class="info-value">{{ $ticket->itMember->name }} (IT Member)</span>
        </div>
        @endif

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Requested Due Date:</span>
            <span class="info-value">{{ $ticket->needed_by->timezone('Asia/Colombo')->format('F j, Y') }}</span>
        </div>
        @endif

        <div class="info-row">
            <span class="info-label">Completed On:</span>
            <span class="info-value">{{ now()->timezone('Asia/Colombo')->format('F j, Y g:i A') }}</span>
        </div>

        @if($ticket->description)
        <div class="info-row" style="display: block; border-bottom: none; padding-top: 15px;">
            <span class="info-label">Description:</span>
            <div class="description-box">
                <p>{{ $ticket->description }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="alert-box" style="background-color: #d1fae5; border-left-color: #10b981;">
        <p><strong>✅ Job Status:</strong> This job has been completed and confirmed by all parties. No further action is required.</p>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Thank you for using the IT Job Management System. If you need further assistance, please submit a new job.
    </p>
@endsection
