@component('mail::message')
# Long Pending Tickets

The following tickets have been pending for a long time:

@foreach ($tickets as $ticket)
- Ticket #{{ $ticket->id }}: {{ $ticket->title }} (Status: {{ $ticket->status }})
@endforeach

Please review and take necessary action.

Thanks,
IT Ticket System
@endcomponent
