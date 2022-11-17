<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FirstLoginCredentials extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@example.com', 'InvenMan Noreply')
            ->subject('Account Activation')
            ->markdown('mails.newuser')
            ->with([
                'link' =>  env('APP_URL').'/activateaccount?email='.$this->email.'&token='.$this->token
            ]);
        //return $this->view('view.name');
    }
}
