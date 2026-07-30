<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PublicContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $contact)
    {
    }

    public function build(): self
    {
        return $this
            ->replyTo($this->contact['email'], trim($this->contact['firstname'].' '.$this->contact['name']))
            ->subject('Nouveau message reçu depuis ProximaJob')
            ->view('emails.public-contact');
    }
}
