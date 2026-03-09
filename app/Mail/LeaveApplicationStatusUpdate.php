<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveApplicationStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $leaveApplication;
    public $status;
    public $remarks;
    public $processedBy;

    public function __construct($user, $leaveApplication, $status, $remarks = null, $processedBy = null)
    {
        $this->user = $user;
        $this->leaveApplication = $leaveApplication;
        $this->status = $status;
        $this->remarks = $remarks;
        $this->processedBy = $processedBy;
    }

    public function envelope()
    {
        $statusText = ucfirst($this->status);
        return new Envelope(
            subject: "Leave Application {$statusText} - ISU-Flow System",
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.leave-application-status-update',
        );
    }

    public function attachments()
    {
        return [];
    }
}
