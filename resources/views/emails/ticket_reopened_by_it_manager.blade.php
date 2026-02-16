@extends('emails.layout')

@section('header')
	<h1>🔄 Ticket Reopened</h1>
	<p>Action Needed - Ticket #{{ $ticket->id }}</p>
@endsection

@section('content')
	<p class="greeting">Hello <strong>{{ $ticket->itMember?->name ?? 'IT Member' }}</strong>,</p>

	<p class="message">
		The IT Manager has reopened the ticket below. Please review the note and continue the work.
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

	<div class="instructions-box">
		<strong>📝 Reopen Note from IT Manager:</strong>
		<p>{{ $remark }}</p>
	</div>

	<div class="button-container">
		<a href="{{ route('dashboard.it_member') }}" class="button button-primary">Go to IT Member Dashboard</a>
		<a href="{{ route('tickets.show', $ticket) }}" class="button button-secondary">View Ticket #{{ $ticket->id }}</a>
	</div>

	<div class="divider"></div>

	<p style="text-align: center; color: #6b7280; font-size: 14px;">
		Please update the ticket status after taking action.
	</p>
@endsection
