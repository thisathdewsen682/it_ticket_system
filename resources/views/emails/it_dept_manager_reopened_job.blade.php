<!DOCTYPE html>
<html>
<head>
    <title>Job Reopened - Reassignment Required</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">
        <div style="background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #ea580c; border-bottom: 2px solid #ea580c; padding-bottom: 10px;">
                Job Reopened by IT Department Manager
            </h2>
            
            <p>Dear IT Manager,</p>
            
            <p>The IT Department Manager has reopened the following ticket. Please review and reassign to an IT member.</p>
            
            <div style="background-color: #fff7ed; padding: 15px; border-left: 4px solid #ea580c; margin: 20px 0;">
                <p style="margin: 5px 0;"><strong>Ticket ID:</strong> #{{ $ticket->id }}</p>
                <p style="margin: 5px 0;"><strong>Title:</strong> {{ $ticket->title }}</p>
                <p style="margin: 5px 0;"><strong>Requester:</strong> {{ $ticket->requester->name ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Previous IT Member:</strong> {{ $ticket->itMember->name ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Category:</strong> {{ $ticket->category }}</p>
                <p style="margin: 5px 0;"><strong>Priority:</strong> {{ $ticket->priority }}</p>
            </div>
            
            <p><strong>Description:</strong></p>
            <p style="background-color: #f9fafb; padding: 10px; border-radius: 5px;">{{ $ticket->description }}</p>
            
            @if($ticket->statusHistories->where('status', 'it_dept_reopened_completion')->last())
                <p><strong>Reopen Reason:</strong></p>
                <p style="background-color: #fee2e2; padding: 10px; border-radius: 5px; color: #991b1b;">
                    {{ $ticket->statusHistories->where('status', 'it_dept_reopened_completion')->last()->remarks ?? 'No reason provided' }}
                </p>
            @endif
            
            <div style="margin-top: 30px; text-align: center;">
                <p>Please log in to the system to reassign this ticket to an IT member.</p>
                <a href="{{ url('/dashboard') }}" style="display: inline-block; padding: 12px 30px; background-color: #ea580c; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 15px;">
                    View in Dashboard
                </a>
            </div>
            
            <hr style="margin-top: 30px; border: none; border-top: 1px solid #e5e7eb;">
            
            <p style="font-size: 12px; color: #6b7280; margin-top: 20px;">
                This is an automated notification from the IT Ticket System. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
