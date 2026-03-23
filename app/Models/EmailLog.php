<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'mailable_class',
        'subject',
        'to',
        'cc',
        'bcc',
        'from',
        'status',
        'error_message',
        'ticket_id',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getShortMailableNameAttribute(): string
    {
        return class_basename($this->mailable_class ?? 'Unknown');
    }
}
