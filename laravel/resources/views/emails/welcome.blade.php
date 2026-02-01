@component('mail::message')
# Welcome to {{ $appName }}

Hi {{ $user->name }},

Your account has been created! Here are your login details:

**Email:** {{ $user->email }}  
**Temporary Password:** {{ $temporaryPassword }}

To access your account, visit: [{{ $appName }}]({{ $loginUrl }})

We recommend changing your password after your first login.

@component('mail::button', ['url' => $loginUrl])
Login to Your Account
@endcomponent

If you have any questions, please don't hesitate to contact our support team.

Thanks,  
{{ $appName }} Team
@endcomponent
