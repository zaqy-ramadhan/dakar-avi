<?php

namespace App\Mail;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivityLogNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $activityLog;

    /**
     * Create a new message instance.
     */
    public function __construct(ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $actor = $this->activityLog->actor;
        $actorName = $actor ? $actor->fullname . ' (' . $actor->npk . ')' : 'System';
        
        return new Envelope(
            subject: $actorName . ' ' . $this->activityLog->note,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activity-log-notification',
            with: [
                'activityLog' => $this->activityLog,
                'actor' => $this->activityLog->actor,
                'employee' => $this->activityLog->employee,
            ],
        );
    }
}
