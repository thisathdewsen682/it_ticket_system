@extends('emails.layout', ['headerColor' => '#7c3aed', 'headerColorDark' => '#6d28d9', 'accentColor' => '#7c3aed'])

@section('header')
    <h1>⏰ Daily Reminder: Approval Confirmation Required</h1>
    <p>IT Department Manager - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Dear <strong>IT Department Manager</strong>,</p>

    <p class="message">
        This is a friendly reminder that the following ticket has been approved by the department manager and is awaiting your confirmation. Please review and confirm this ticket before it proceeds to the IT Manager for assignment.
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
            <span class="info-value">{{ $ticket->approvalUser->name ?? 'N/A' }} (Department Manager)</span>
        </div>

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value">
                <strong>{{ $ticket->needed_by->format('F j, Y') }}</strong>
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
            <li>Review the approved ticket details</li>
            <li>Click "Confirm" to send to IT Manager for assignment</li>
            <li>Click "Reject" if the ticket needs revision</li>
        </ul>
    </div>

    <div class="footer-note" style="margin-top: 20px; background-color: #fef3c7; border-left: 4px solid #f59e0b;">
        <p>📌 <strong>Note:</strong> This is a daily reminder. You will continue to receive this email until action is taken on this ticket.</p>
    </div>

    <div class="footer-note" style="margin-top: 15px; background-color: #dbeafe; border-left: 4px solid #3b82f6;">
        <p>⚡ <strong>Workflow:</strong> Department Manager → <strong style="color: #7c3aed;">IT Dept Manager (You)</strong> → IT Manager → IT Member Assignment</p>
    </div>
@endsection
