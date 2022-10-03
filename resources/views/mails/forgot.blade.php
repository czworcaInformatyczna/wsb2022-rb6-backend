@component('mail::message')
You have requested a password reset  
Click below to reset your password
@component('mail::button', ['url' => $link])
Click here to set new password
@endcomponent
If you did not request this password reset you can safely delete this mail.
Sincerely, Team InvenMan.
@endcomponent