<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $passwordChanged;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param bool $passwordChanged
     * @return void
     */
    public function __construct($user, $passwordChanged = false)
    {
        $this->user = $user;
        $this->passwordChanged = $passwordChanged;

        if ($passwordChanged) {
            $this->subject = 'Your Password Has Been Changed';
        } else {
            $this->subject = 'Your Account Information Has Been Updated';
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = $this->passwordChanged 
        ? 'emails.password_changed' 
        : 'emails.account.updated';

return $this->subject('Your Account Information Has Been Updated')
            ->view($view);
    }
}
