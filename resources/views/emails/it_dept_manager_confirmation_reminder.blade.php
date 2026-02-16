@extends('emails.layout', ['headerColor' => '#7c3aed', 'headerColorDark' => '#6d28d9', 'accentColor' => '#7c3aed'])

@section('header')
    <h1>⏰ Daily Reminder: Confirmation Required</h1>
    <p>IT Department Manager - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>IT Department Manager</strong>,</p>

    <p class="message">
        This is a friendly reminder that the following job has been confirmed by the IT Manager and is awaiting your confirmation. Please review and confirm the completion, or reopen if further work is required.
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
            <span class="info-label">Handled By:</span>
            <span class="info-value">{{ $ticket->itMember->name ?? 'N/A' }} (IT Member)</span>
        </div>

        <div class="info-row">
            <span class="info-label">Department Manager:</span>
            <span class="info-value">{{ $ticket->approvalUser->name ?? 'N/A' }}</span>
        </div>

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Needed By:</span>
            <span class="info-value">
                <strong>{{ $ticket->needed_by->timezone('Asia/Colombo')->format('F j, Y') }}</strong>
                @php
                    $daysRemaining = now()->startOfDay()->diffInDays($ticket->needed_by->startOfDay(), false);
                @endphp
                @if($daysRemaining < 0)
                    <span class="text-red-600">({{ abs($daysRemaining) }} days overdue)</span>
                @elseif($daysRemaining === 0)
                    <span class="text-orange-600">(Due today!)</span>
                @elseif($daysRemaining <= 2)
                    <span class="text-orange-600">({{ $daysRemaining }} days remaining)</span>
                @else
                    <span class="text-gray-600">({{ $daysRemaining }} days remaining)</span>
                @endif
            </span>
        </div>
        @endif

        @if($ticket->description)
        <div class="info-row">
            <span class="info-label">Description:</span>
            <span class="info-value">{{ Str::limit($ticket->description, 200) }}</span>
        </div>
        @endif
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('dashboard') }}" class="button">
            View in Dashboard
        </a>
    </div>

    <div class="footer-note">
        <p><strong>Action Required:</strong></p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Review the completed work</li>
            <li>Click "Confirm Completion" if satisfied</li>
            <li>Click "Reopen" if more work is needed</li>
        </ul>
    </div>

    <div class="footer-note" style="margin-top: 20px; background-color: #fef3c7; border-left: 4px solid #f59e0b;">
        <p>📌 <strong>Note:</strong> This is a daily reminder. You will continue to receive this email until action is taken on this job.</p>
    </div>
@endsection
