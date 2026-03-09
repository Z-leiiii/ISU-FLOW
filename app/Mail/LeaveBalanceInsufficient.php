<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveBalanceInsufficient extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $leaveType;
    public $requestedDays;
    public $availableBalance;
    public $computationDetails;

    public function __construct($user, $leaveType, $requestedDays, $availableBalance, $computationDetails)
    {
        $this->user = $user;
        $this->leaveType = $leaveType;
        $this->requestedDays = $requestedDays;
        $this->availableBalance = $availableBalance;
        $this->computationDetails = $computationDetails;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Insufficient Leave Balance - ISU-Flow System',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.leave-balance-insufficient',
        );
    }

    public function attachments()
    {
        return [];
    }
}
