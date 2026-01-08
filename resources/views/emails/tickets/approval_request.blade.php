<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket Approval Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #111827;">
    <p>Hello,</p>

    <p>
        You have a new IT ticket pending your approval.
    </p>

    <p>
        <strong>Ticket #{{ $ticket->id }}</strong><br>
        <strong>Title:</strong> {{ $ticket->title }}<br>
        <strong>Category:</strong> {{ $ticket->category }}<br>
        <strong>Priority:</strong> {{ $ticket->priority }}<br>
        @if($ticket->needed_by)
            <strong>Due date:</strong> {{ $ticket->needed_by->format('Y-m-d H:i') }}<br>
        @endif
    </p>

    @if($approvalCutoff)
        <p>
            <strong>Approval deadline:</strong> {{ $approvalCutoff->format('Y-m-d 23:59') }}
        </p>
    @endif

    <p>
        Approve: <a href="{{ $approveUrl }}">Click to approve</a><br>
        Reject: <a href="{{ $rejectUrl }}">Click to reject</a>
    </p>

    <p>
        If the links have expired, you won’t be able to approve/reject after the due date.
    </p>

    <p>
        Thanks,<br>
        {{ config('app.name') }}
    </p>
</body>
</html>
