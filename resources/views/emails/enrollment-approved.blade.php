@component('mail::message')
# Enrollment Approved!

Dear {{ $user->first_name }},

Great news! Your enrollment for the following course has been approved:

@component('mail::panel')
**Course:** {{ $course->title }}  
**Status:** Approved  
**Approval Date:** {{ now()->format('F j, Y g:i A') }}
@endcomponent

## Access Your Course

You can now access your course materials through the Moodle Learning Management System.

@component('mail::button', ['url' => $moodleUrl])
Access Moodle LMS
@endcomponent

@component('mail::button', ['url' => route('mycourses')])
View My Courses
@endcomponent

If you need any assistance accessing your course, please don't hesitate to contact our support team.

Happy learning!

Best regards,  
Ministry of Health Training Team
@endcomponent