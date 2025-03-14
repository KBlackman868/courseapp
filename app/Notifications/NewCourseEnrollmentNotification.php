<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue; // Optional: for queued notifications
use Illuminate\Notifications\Messages\MailMessage;

class NewCourseEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $enrollment;

    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // You can include database, broadcast, etc.
    }

    public function toMail($notifiable)
    {
        $url = route('admin.enrollments'); // Link to the admin dashboard

        return (new MailMessage)
                    ->subject('New Course Enrollment Request')
                    ->line('A new enrollment request has been submitted.')
                    ->action('Review Enrollment', $url)
                    ->line('Please review and approve or deny the request.');
    }

    public function toArray($notifiable)
    {
        return [
            'enrollment_id' => $this->enrollment->id,
            'user'          => $this->enrollment->user->name,
            'course'        => $this->enrollment->course->title,
        ];
    }
}
