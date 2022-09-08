<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $recoveryToken)
    {
        $this->email = $email;
        $this->recoveryToken = $recoveryToken;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@example.com', 'InvenMan Noreply')
            ->subject('Password recovery')
            ->markdown('mails.forgot')
            ->with([
                'link' =>  env('APP_URL').'/resetpassword?email='.$this->email.'&token='.$this->recoveryToken
            ]);
    }
}
