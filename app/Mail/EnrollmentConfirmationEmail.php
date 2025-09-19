<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Enrollment $enrollment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Request Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enrollment-confirmation',
            with: [
                'enrollment' => $this->enrollment,
                'user' => $this->enrollment->user,
                'course' => $this->enrollment->course,
            ]
        );
    }
}