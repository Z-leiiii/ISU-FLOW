<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $leaveApplication;
    public $hrEmails;

    public function __construct($user, $leaveApplication, $hrEmails = [])
    {
        $this->user = $user;
        $this->leaveApplication = $leaveApplication;
        $this->hrEmails = $hrEmails;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'New Leave Application Submitted - ISU-Flow System',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.leave-application-submitted',
        );
    }

    public function attachments()
    {
        return [];
    }
}
