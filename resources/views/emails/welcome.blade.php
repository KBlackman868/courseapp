
@component('mail::message')
# Welcome to Ministry of Health Learning Management System!

Dear {{ $user->first_name }} {{ $user->last_name }},

Thank you for registering with the Ministry of Health LMS. Your account has been successfully created.

## Your Login Credentials

@component('mail::panel')
**Email:** {{ $user->email }}  
**Password:** {{ $password }}  
**Department:** {{ $user->department }}
@endcomponent

@component('mail::button', ['url' => $loginUrl])
Login to Your Account
@endcomponent

## Important Security Information

- Please change your password after your first login
- Keep your login credentials secure and do not share them with others
- Your account will be synced with Moodle LMS upon your first course enrollment

## Next Steps

1. **Verify your email** - Check your inbox for the verification email
2. **Complete your profile** - Add your profile photo and additional information
3. **Browse courses** - Explore available training modules
4. **Enroll in courses** - Start your learning journey

If you have any questions or need assistance, please contact our support team.

Best regards,  
Ministry of Health Training Team

@component('mail::subcopy')
If you're having trouble clicking the "Login to Your Account" button, copy and paste the URL below into your web browser: {{ $loginUrl }}
@endcomponent
@endcomponent