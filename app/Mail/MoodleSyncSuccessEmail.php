<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MoodleSyncSuccessEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Moodle Account Created Successfully',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.moodle-sync-success',
            with: [
                'user' => $this->user,
                'moodleUrl' => config('services.moodle.url', '#'),
            ]
        );
    }
}