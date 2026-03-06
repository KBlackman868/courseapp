<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your MOH Learning Account Has Been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-approved',
            with: [
                'user' => $this->user,
                'loginUrl' => config('app.url', 'https://mohlearn.hin.gov.tt') . '/login',
            ]
        );
    }
}
