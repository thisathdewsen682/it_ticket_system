<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ItDeptManagerReopenedJobMail extends QueuedMailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('Job Reopened by IT Dept Manager - Job #' . $this->ticket->id)
            ->view('emails.it_dept_manager_reopened_job');
    }
}
