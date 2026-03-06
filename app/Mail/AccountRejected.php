<?php

namespace App\Mail;

use App\Models\AccountRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public AccountRequest $accountRequest,
        public string $reason
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MOH Learning Account Request Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-rejected',
            with: [
                'accountRequest' => $this->accountRequest,
                'reason' => $this->reason,
            ]
        );
    }
}
