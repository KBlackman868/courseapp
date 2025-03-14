<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue; // Optional: for queued notifications
use Illuminate\Notifications\Messages\MailMessage;

class EnrollmentApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $enrollment;

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        // Provide the Moodle website link
        $moodleUrl = 'https://moodle.example.com';  // Replace with your actual Moodle URL

        return (new MailMessage)
                    ->subject('Course Enrollment Approved')
                    ->line('Your enrollment for the course "' . $this->enrollment->course->title . '" has been approved.')
                    ->action('Go to Moodle', $moodleUrl)
                    ->line('Thank you for your patience.');
    }

    public function toArray($notifiable)
    {
        return [
            'enrollment_id' => $this->enrollment->id,
            'course'        => $this->enrollment->course->title,
            'status'        => $this->enrollment->status,
        ];
    }
}
