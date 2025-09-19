<?php

@component('mail::message')
# Enrollment Request Received

Dear {{ $enrollment->user->first_name }},

We have received your enrollment request for the following course:

@component('mail::panel')
**Course:** {{ $enrollment->course->title }}  
**Status:** Pending Approval  
**Request Date:** {{ $enrollment->created_at->format('F j, Y g:i A') }}
@endcomponent

## What Happens Next?

Your enrollment request will be reviewed by our administrators. You will receive an email notification once your enrollment is approved.

**Typical processing time:** 1-2 business days

@component('mail::button', ['url' => route('mycourses')])
View My Courses
@endcomponent

If you have any urgent questions about your enrollment, please contact the training department.

Thank you for your interest in continuing education!

Best regards,  
Ministry of Health Training Team
@endcomponent