<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userId;
    public $tempPassword;
    public $loginLink;

    public function __construct($userId, $tempPassword, $loginLink)
    {
        $this->userId = $userId;
        $this->tempPassword = $tempPassword;
        $this->loginLink = $loginLink;
    }

    public function build()
    {
        return $this
            ->subject('Your Account Has Been Registered')
            ->markdown('emails.student.registered');
    }
}
