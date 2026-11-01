<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Postulation;

class ApplicationStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $postulation;
    public $statusMessage;

    /**
     * @param  string  $statusMessage  Formulation lisible du nouveau statut
     *                                 (ex. « présélectionnée », « retenue pour un entretien »),
     *                                 produite par CandidatureController::getStatusMessage().
     */
    public function __construct(Postulation $postulation, string $statusMessage)
    {
        $this->postulation = $postulation->loadMissing(['user', 'offre.entreprise']);
        $this->statusMessage = $statusMessage;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $candidate = $this->postulation->user;
        $offer = $this->postulation->offre;

        return $this->subject('Votre candidature a été ' . $this->statusMessage . ' - ProximaJob')
                    ->view('emails.application-status-updated')
                    ->with([
                        'candidateName' => trim(($candidate->name ?? '') . ' ' . ($candidate->prenom ?? '')),
                        'offerTitle' => $offer?->titre ?? 'une offre',
                        'companyName' => $offer?->entreprise?->company_name ?? 'l’entreprise',
                        'statusMessage' => $this->statusMessage,
                        'detailUrl' => route('user.detail-candidature', ['id' => $this->postulation->id]),
                    ]);
    }
}
