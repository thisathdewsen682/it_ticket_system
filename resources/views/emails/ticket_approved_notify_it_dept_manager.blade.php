@extends('emails.layout', ['headerColor' => '#7c3aed', 'headerColorDark' => '#6d28d9', 'accentColor' => '#7c3aed'])

@section('header')
    <h1>✅ Job Approved - Confirmation Required</h1>
    <p>IT Department Manager Action Needed - Job #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>IT Department Manager</strong>,</p>

    <p class="message">
        A Job has been approved by the department manager. As IT Department Manager, please review and confirm this job before it proceeds to the IT Manager for assignment.
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
            <span class="info-label">Approved By:</span>
            <span class="info-value">{{ $ticket->approvalUser->name ?? 'N/A' }}</span>
        </div>

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value" style="color: #dc2626; font-weight: 600;">
                {{ $ticket->needed_by->timezone('Asia/Colombo')->format('F j, Y') }}
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
        <p><strong>⚠️ Action Required:</strong> Please confirm or reject this job. Once confirmed, it will be sent to the IT Manager for assignment.</p>
    </div>

    <div class="button-container">
        <a href="{{ url('/dashboard/unified?role_tab=it-dept-manager&tab=pending') }}" class="button button-primary">Review Job</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Log in to your IT Department Manager dashboard to confirm or reject this job.
    </p>
@endsection
