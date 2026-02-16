<?php

namespace App\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

abstract class QueuedMailable extends Mailable implements ShouldQueue
{
    public int $tries = 5;
    public array $backoff = [60, 300, 900];
}
