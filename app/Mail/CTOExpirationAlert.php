<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CTOExpirationAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $cto;
    public $daysThreshold;

    public function __construct($user, $cto, $daysThreshold)
    {
        $this->user = $user;
        $this->cto = $cto;
        $this->daysThreshold = $daysThreshold;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'CTO Expiration Alert - ISU-Flow System',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.cto-expiration-alert',
        );
    }

    public function attachments()
    {
        return [];
    }
}
