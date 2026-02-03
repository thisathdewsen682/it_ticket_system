<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ItManagerConfirmedNotifyItDeptManagerMail extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('Job Completion Awaits Your Confirmation - Ticket #' . $this->ticket->id)
            ->view('emails.it_manager_confirmed_notify_it_dept_manager');
    }
}
