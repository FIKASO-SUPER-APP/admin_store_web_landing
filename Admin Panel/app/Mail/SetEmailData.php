<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SetEmailData extends Mailable
{
    use Queueable, SerializesModels;

    public $dynamicSubject;
    public $dynamicMessage;

    public function __construct($subject, $message, $fromAddress = null, $fromName = null)
    {
        $this->dynamicSubject = $subject;
        $this->dynamicMessage = $message;
        $this->fromAddress = $fromAddress ?? config('mail.from.address');
        $this->fromName = $fromName ?? config('mail.from.name');
    }

    public function build()
    {
        return $this->subject($this->dynamicSubject)
                    ->from($this->fromAddress, $this->fromName)
                    ->view('settings.email.send_email')
                    ->with('data', $this->dynamicMessage);
    }
}


?>