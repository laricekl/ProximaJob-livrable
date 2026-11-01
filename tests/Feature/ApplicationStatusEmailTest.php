<?php

namespace Tests\Feature;

use App\Mail\ApplicationStatusUpdated;
use App\Models\Notification;
use App\Models\Postulation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

/**
 * Courriel au candidat lors d'un changement de statut (Phase 0, 31 oct. 2026).
 * Verrouille aussi la déduplication de la notification interne.
 */
class ApplicationStatusEmailTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    public function test_status_update_sends_one_email_and_one_internal_notification(): void
    {
        Mail::fake();

        $enterpriseUser = $this->createEnterprise();
        $offer = $this->createOfferFor($enterpriseUser);
        $candidate = $this->createCandidate();
        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'status' => 'en_attente',
            'autopostulation' => false,
        ]);

        $response = $this->actingAs($enterpriseUser)->put(
            route('candidature.updateStatus', $postulation),
            ['status' => 'shortlisted']
        );

        $response->assertRedirect();
        $this->assertSame('shortlisted', $postulation->refresh()->status);

        Mail::assertSent(ApplicationStatusUpdated::class, function ($mail) use ($candidate) {
            return $mail->hasTo($candidate->email);
        });

        // L'ancien code créait la notification dans un if/else dupliqué ;
        // on verrouille le fait qu'il n'y en a qu'UNE par changement de statut.
        $this->assertSame(1, Notification::where('user_id', $candidate->id)
            ->where('type', 'application')->count());
    }

    public function test_status_email_has_candidate_friendly_subject(): void
    {
        $enterpriseUser = $this->createEnterprise();
        $offer = $this->createOfferFor($enterpriseUser);
        $candidate = $this->createCandidate();
        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'status' => 'en_attente',
            'autopostulation' => true,
        ]);

        $mailable = new ApplicationStatusUpdated($postulation, 'présélectionnée');
        $mailable->build();

        $this->assertSame(
            'Votre candidature a été présélectionnée - ProximaJob',
            $mailable->subject
        );
    }
}
