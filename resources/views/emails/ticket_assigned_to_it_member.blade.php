@extends('emails.layout')

@section('header')
    <h1>🎯 New Job Assigned</h1>
    <p>Job #{{ $ticket->id }}</p>
@endsection

@section('content')
    <p class="greeting">Hello <strong>{{ $ticket->itMember->name }}</strong>,</p>

    <p class="message">
        A new job has been assigned to you by the IT Manager. Please review the details below and start working on it as soon as possible.
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

        @if($ticket->needed_by)
        <div class="info-row">
            <span class="info-label">Job Due Date:</span>
            <span class="info-value" style="color: #dc2626; font-weight: 600;">
                {{ $ticket->needed_by->timezone('Asia/Colombo')->format('F j, Y') }}
            </span>
        </div>
        @endif

        @if($ticket->it_due_at)
        <div class="info-row">
            <span class="info-label">IT Completion Date:</span>
            <span class="info-value" style="color: #dc2626; font-weight: 600;">
                {{ \Carbon\Carbon::parse($ticket->it_due_at)->timezone('Asia/Colombo')->format('F j, Y - H:i') }}
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

    @if($ticket->it_instructions)
    <div class="instructions-box">
        <strong>📝 IT Manager Instructions:</strong>
        <p>{{ $ticket->it_instructions }}</p>
    </div>
    @endif

    <div class="button-container">
        <a href="{{ url('/dashboard') }}" class="button button-primary">🚀 Go to Dashboard</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        Please log in to your dashboard to start working on this job and update its status as you progress.
    </p>
@endsection
