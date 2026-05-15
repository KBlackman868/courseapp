<?php

namespace App\Mail;

use App\Models\AccountRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAccountRequestAdminEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public AccountRequest $accountRequest
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Account Request Awaiting Approval - MOH Learning',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-account-request-admin',
            with: [
                'accountRequest' => $this->accountRequest,
                'reviewUrl' => route('admin.account-requests.index'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
