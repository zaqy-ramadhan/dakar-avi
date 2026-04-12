<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractExpiryReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $employees;
    public $attachmentPath;
    public $monthInfo;

    /**
     * Create a new message instance.
     */
    public function __construct($employees, $attachmentPath, $monthInfo = null)
    {
        $this->employees = $employees;
        $this->attachmentPath = $attachmentPath;
        $this->monthInfo = $monthInfo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat Perbaruan Kontrak Karyawan - ' . now()->format('F Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-expiry-reminder',
            with: [
                'employees' => $this->employees,
                'totalEmployees' => count($this->employees),
                'monthInfo' => $this->monthInfo,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->attachmentPath)
                ->as('Contract_Expiry_Reminder_' . now()->format('Y-m-d') . '.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
