@component('mail::message')
You have been added to InvenMan.
Click below to activate your account.
@component('mail::button', ['url' => $link])
Click here to activate account
@endcomponent
If you did not request this password reset you can safely delete this mail.
Sincerely, Team InvenMan.
@endcomponent