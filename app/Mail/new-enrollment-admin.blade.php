@component('mail::message')
# New Enrollment Request

A new enrollment request has been submitted and requires your approval.

## Student Details

@component('mail::panel')
**Name:** {{ $user->first_name }} {{ $user->last_name }}  
**Email:** {{ $user->email }}  
**Department:** {{ $user->department }}
@endcomponent

## Course Information

@component('mail::panel')
**Course Title:** {{ $course->title }}  
**Request Date:** {{ $enrollment->created_at->format('F j, Y g:i A') }}  
**Status:** Pending Approval
@endcomponent

@component('mail::button', ['url' => $approvalUrl])
Review Enrollment Request
@endcomponent

Please review and approve or deny this enrollment request at your earliest convenience.

Best regards,  
Ministry of Health LMS System
@endcomponent