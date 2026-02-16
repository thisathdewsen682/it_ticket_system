@extends('emails.layout', ['headerColor' => '#f97316', 'headerColorDark' => '#ea580c', 'accentColor' => '#f97316'])

@section('header')
    <h1>⏰ Daily Reminder</h1>
    <p>{{ $tickets->count() }} Tickets Awaiting Assignment/Reassignment</p>
@endsection

@section('content')
    <p class="greeting">Good Morning <strong>IT Manager</strong>,</p>

    <p class="message">
        You have <strong>{{ $tickets->count() }} ticket(s)</strong> awaiting assignment or reassignment. Please review and assign an IT member to ensure timely completion.
    </p>

    <div class="info-card">
        <h3>📋 Tickets Awaiting Assignment/Reassignment</h3>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 10px; text-align: left; font-size: 12px; color: #6b7280; font-weight: 600;">ID</th>
                    <th style="padding: 10px; text-align: left; font-size: 12px; color: #6b7280; font-weight: 600;">Title</th>
                    <th style="padding: 10px; text-align: left; font-size: 12px; color: #6b7280; font-weight: 600;">Priority</th>
                    <th style="padding: 10px; text-align: left; font-size: 12px; color: #6b7280; font-weight: 600;">Due Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px; font-size: 14px; color: #111827; font-weight: 600;">#{{ $ticket->id }}</td>
                    <td style="padding: 10px; font-size: 14px; color: #374151;">{{ \Str::limit($ticket->title, 40) }}</td>
                    <td style="padding: 10px; font-size: 12px;">
                        <span class="badge badge-{{ strtolower($ticket->priority) }}">{{ $ticket->priority }}</span>
                    </td>
                    <td style="padding: 10px; font-size: 14px; color: {{ $ticket->needed_by && now()->greaterThan($ticket->needed_by) ? '#dc2626' : '#374151' }};">
                        {{ $ticket->needed_by ? $ticket->needed_by->timezone('Asia/Colombo')->format('M j, Y') : '-' }}
                        @if($ticket->needed_by && now()->greaterThan($ticket->needed_by))
                            <span class="badge badge-urgent">OVERDUE</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="alert-box">
        <p><strong>⚠️ Action Required:</strong> These tickets need IT member assignment/reassignment to proceed.</p>
    </div>

    <div class="button-container">
        <a href="{{ url('/dashboard/it-manager?tab=approved') }}" class="button button-primary">Assign IT Members Now</a>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; color: #6b7280; font-size: 14px;">
        This is your daily reminder to keep tickets moving. Assign IT members to ensure timely completion.
    </p>
@endsection
