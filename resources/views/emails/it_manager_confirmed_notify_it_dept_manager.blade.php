<!DOCTYPE html>
<html>
<head>
    <title>Job Completion Awaits Your Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">
        <div style="background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #7c3aed; border-bottom: 2px solid #7c3aed; padding-bottom: 10px;">
                Job Completion Awaits Your Confirmation
            </h2>
            
            <p>Dear IT Department Manager,</p>
            
            <p>The IT Manager has confirmed the completion of the following ticket. Please review and confirm or reopen if needed.</p>
            
            <div style="background-color: #f9fafb; padding: 15px; border-left: 4px solid #7c3aed; margin: 20px 0;">
                <p style="margin: 5px 0;"><strong>Ticket ID:</strong> #{{ $ticket->id }}</p>
                <p style="margin: 5px 0;"><strong>Title:</strong> {{ $ticket->title }}</p>
                <p style="margin: 5px 0;"><strong>Requester:</strong> {{ $ticket->requester->name ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>IT Member:</strong> {{ $ticket->itMember->name ?? 'N/A' }}</p>
                <p style="margin: 5px 0;"><strong>Category:</strong> {{ $ticket->category }}</p>
                <p style="margin: 5px 0;"><strong>Priority:</strong> {{ $ticket->priority }}</p>
            </div>
            
            <p><strong>Description:</strong></p>
            <p style="background-color: #f9fafb; padding: 10px; border-radius: 5px;">{{ $ticket->description }}</p>
            
            <div style="margin-top: 30px; text-align: center;">
                <p>Please log in to the system to review and confirm the completion or reopen if necessary.</p>
                <a href="{{ url('/dashboard') }}" style="display: inline-block; padding: 12px 30px; background-color: #7c3aed; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 15px;">
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
