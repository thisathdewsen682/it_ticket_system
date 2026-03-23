<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeNewUserMail extends QueuedMailable
{
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $temporaryPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your IT Job System Account Has Been Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_new_user',
        );
    }
}
