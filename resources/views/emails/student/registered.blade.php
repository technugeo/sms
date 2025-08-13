@component('mail::message')
# Welcome to {{ config('app.name') }}

Dear Student,

Your academic status is now **Registered**.

Here are your login details:

**User ID:** {{ $userId }}

**Temporary Password:** {{ $tempPassword }}

Please log in using the button below and change your password immediately.

@component('mail::button', ['url' => $loginLink])
Login Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
