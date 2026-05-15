<?php

namespace App\Mail;

use App\Models\CourseAccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCourseAccessRequestAdminEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public CourseAccessRequest $courseAccessRequest
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Course Access Request Awaiting Approval - MOH Learning',
        );
    }

    public function content(): Content
    {
        $this->courseAccessRequest->load(['user', 'course']);

        return new Content(
            view: 'emails.new-course-access-request-admin',
            with: [
                'courseAccessRequest' => $this->courseAccessRequest,
                'user' => $this->courseAccessRequest->user,
                'course' => $this->courseAccessRequest->course,
                'reviewUrl' => route('admin.course-access-requests.index'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
