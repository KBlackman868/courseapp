@component('mail::message')
# Moodle Account Created

Dear {{ $user->first_name }},

Your Moodle Learning Management System account has been successfully created!

## Your Account Details

@component('mail::panel')
**Username:** {{ $user->email }}  
**Moodle User ID:** {{ $user->moodle_user_id }}
@endcomponent

You can now access all your enrolled courses through the Moodle platform.

@component('mail::button', ['url' => $moodleUrl])
Access Moodle LMS
@endcomponent

## Need Help?

If you have trouble accessing your account or need to reset your password, please contact our IT support team.

Best regards,  
Ministry of Health Training Team
@endcomponent