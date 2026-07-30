<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $campaignSubject,
        public readonly string $campaignContent,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject($this->campaignSubject)
            ->view('emails.newsletter-campaign');
    }
}
