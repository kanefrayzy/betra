<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $newPassword;

    /**
     * Create a new message instance.
     *
     * @param string $newPassword
     * @return void
     */
    public function __construct($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    /**
     * Build message for sending.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.new-password')
                    ->subject(__('home.new_password_subject'));
    }
}
