<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivityLogDailySummary extends Mailable
{
    use Queueable, SerializesModels;

    public $activities;
    public $summaryDate;
    public $stats;

    /**
     * Create a new message instance.
     */
    public function __construct($activities, Carbon $summaryDate, array $stats)
    {
        $this->activities = $activities;
        $this->summaryDate = $summaryDate;
        $this->stats = $stats;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $dateFormatted = $this->summaryDate->format('d F Y');
        
        return new Envelope(
            subject: "📊 Activity Log Summary - {$dateFormatted}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activity-log-daily-summary',
            with: [
                'activities' => $this->activities,
                'summaryDate' => $this->summaryDate,
                'stats' => $this->stats,
                'categoryLabels' => $this->getCategoryLabels(),
            ],
        );
    }

    /**
     * Get human-readable labels for categories
     */
    private function getCategoryLabels(): array
    {
        return [
            'dakar_users' => '👤 Users',
            'dakar_employee_details' => '📋 Employee Details',
            'dakar_employee_banks' => '🏦 Employee Bank Accounts',
            'dakar_employee_educations' => '🎓 Employee Education',
            'dakar_employee_documents' => '📄 Employee Documents',
            'dakar_employee_jobs' => '💼 Employee Jobs',
            'dakar_events' => '📅 Events',
        ];
    }
}
