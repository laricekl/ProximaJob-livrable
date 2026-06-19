<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EntrepriseValidated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $entreprise;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->entreprise = $user->entreprise;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Votre compte entreprise a été validé - Proximalob')
                    ->view('emails.entreprise-validated')
                    ->with([
                        'userName' => $this->user->name . ' ' . $this->user->prenom,
                        'companyName' => $this->entreprise->company_name,
                    ]);
    }
}