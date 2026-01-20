<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'requester_id',
        'approval_user_id',
        'it_member_id',
        'title',
        'description',
        'category',
        'priority',
        'needed_by',
        'it_due_at',
        'affected_user',
        'location',
        'it_instructions',
        'asset_tag',
        'device_name',
        'ip_address',
        'system_name',
        'access_role',
        'access_start_date',
        'access_end_date',
        'incident_started_at',
        'steps_to_reproduce',
        'error_message',
        'impact',
        'attachment_path',
        'attachment_original_name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'needed_by' => 'datetime',
            'it_due_at' => 'datetime',
            'access_start_date' => 'date',
            'access_end_date' => 'date',
            'incident_started_at' => 'datetime',
        ];
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approvalUser()
    {
        return $this->belongsTo(User::class, 'approval_user_id');
    }

    public function itMember()
    {
        return $this->belongsTo(User::class, 'it_member_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(TicketStatusHistory::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }
}