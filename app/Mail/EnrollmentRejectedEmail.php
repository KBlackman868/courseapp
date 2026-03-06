<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentRejectedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Enrollment $enrollment,
        public string $reason = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Course Enrollment Update - MOH Learning',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-rejected',
            with: [
                'enrollment' => $this->enrollment,
                'user' => $this->enrollment->user,
                'course' => $this->enrollment->course,
                'reason' => $this->reason,
            ]
        );
    }
}
