@component('mail::message')
    You have requested a password reset
    This is your new password

    {{ $newpassword }}

    Sincerely, Team InvenMan.
@endcomponent
