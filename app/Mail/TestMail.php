<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $msg;

    public function __construct($msg)
    {
        $this->msg = $msg;
    }

    public function build()
    {
        return $this->subject('Testing Subject of Mail')
                    ->view('emails.test')   // make a blade template
                    ->with(['msg' => $this->msg]);
    }
}

