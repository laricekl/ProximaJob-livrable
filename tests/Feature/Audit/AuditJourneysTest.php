<?php

namespace Tests\Feature\Audit;

use App\Mail\ApplicationStatusUpdated;
use App\Models\Abonnement;
use App\Models\Entreprise;
use App\Models\Notification;
use App\Models\Postulation;
use App\Models\User;
use App\Models\UserAbonnement;
use App\Services\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

/**
 * Fausse passerelle de paiement pour l'audit : aucun appel Stripe réel.
 */
class AuditFakeGateway implements PaymentGateway
{
    public array $nextEvent = [
        'type' => 'checkout.session.completed',
        'session_id' => 'cs_audit_123',
        'payment_intent' => 'pi_audit_123',
        'user_abonnement_id' => null,
    ];

    public function createCheckoutSession(UserAbonnement $subscription): array
    {
        return ['id' => 'cs_audit_123', 'url' => 'https://checkout.stripe.test/pay/cs_audit_123'];
    }

    public function parseWebhookEvent(string $payload, string $signature): array
    {
        return $this->nextEvent;
    }
}

/**
 * AUDIT — Passe 4 : parcours de bout en bout.
 *
 * Chaque étape est consignée dans journeys-results.jsonl (statut +
 * redirection). Les assertions ne portent que sur les points durs du
 * parcours ; le reste est du constat pour le rapport d'audit.
 */
class AuditJourneysTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    private string $logFile = '/home/hatch/workspace/goals/proximajob-development/hidden_files/journeys-results.jsonl';

    private function step(string $journey, string $step, TestResponse|int|string $response, string $note = ''): void
    {
        $row = ['journey' => $journey, 'step' => $step, 'note' => $note];

        if ($response instanceof TestResponse) {
            $row['status'] = $response->getStatusCode();
            $location = $response->headers->get('Location');
            $row['location'] = $location ? parse_url($location, PHP_URL_PATH) : null;
        } else {
            $row['status'] = $response;
        }

        file_put_contents($this->logFile, json_encode($row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n", FILE_APPEND | LOCK_EX);
    }

    private function verifyEmailViaSignedUrl(User $user): TestResponse
    {
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        return $this->get($url);
    }

    public function test_journey_candidate_register_to_application(): void
    {
        $j = 'candidat';

        // 1. Inscription
        $response = $this->post('/register', [
            'name' => 'Parcours',
            'prenom' => 'Audit',
            'email' => 'parcours-candidat@example.com',
            'telephone' => '5550001111',
            'adresse' => '1 rue Audit',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $this->step($j, 'POST /register', $response);
        $user = User::where('email', 'parcours-candidat@example.com')->first();
        $this->assertNotNull($user, 'Le compte candidat devrait exister après inscription');
        $this->step($j, 'compte créé, courriel vérifié ?', $user->email_verified_at ? 'oui' : 'non', 'rôle: '.implode(',', $user->getRoleNames()->all()));

        // 2. Connexion (le compte n'est pas connecté automatiquement)
        $response = $this->post('/login', ['email' => 'parcours-candidat@example.com', 'password' => 'password']);
        $this->step($j, 'POST /login (avant vérification)', $response);

        // 3. Vérification du courriel par le lien signé
        $response = $this->verifyEmailViaSignedUrl($user);
        $this->step($j, 'GET lien de vérification signé', $response);
        $this->assertNotNull($user->fresh()->email_verified_at, 'Le courriel devrait être vérifié après le lien signé');

        // 4. Tableau de bord sans CV : entonnoir attendu vers le formulaire CV
        $response = $this->actingAs($user)->get(route('dashboard'));
        $this->step($j, 'GET /dashboard (sans CV)', $response);
        $response = $this->get(route('infos.cv'));
        $this->step($j, 'GET /user/infos-cv', $response);

        // 5. Création du profil CV (avec une expérience)
        $response = $this->post(route('cv.store'), [
            'nom' => 'Parcours',
            'prenom_cv' => 'Audit',
            'email_cv' => 'parcours-candidat@example.com',
            'telephone_cv' => '5550001111',
            'experiences' => [
                ['periode' => '2022-2026', 'poste' => 'Développeur Laravel', 'entreprise' => 'Audit Inc.', 'description' => 'Parcours de test'],
            ],
        ]);
        $this->step($j, 'POST /cv/store (profil + expérience)', $response);
        $hasExperience = $user->fresh()->cvProfile?->experiences()->exists() ?? false;
        $this->step($j, 'expérience enregistrée ?', $hasExperience ? 'oui' : 'non');

        // 6. Tableau de bord avec CV complété (session fraîche, comme un vrai retour)
        $this->post('/logout');
        $this->post('/login', ['email' => 'parcours-candidat@example.com', 'password' => 'password']);
        $response = $this->get(route('dashboard'));
        $this->step($j, 'GET /dashboard (avec CV, session fraîche)', $response);
        $response = $this->get(route('user.home'));
        $this->step($j, 'GET /user (accueil candidat)', $response);

        // 7. Postuler à une offre (entreprise + offre en fixture)
        $enterpriseUser = $this->createEnterprise(['email' => 'parcours-entreprise@example.com']);
        $offer = $this->createOfferFor($enterpriseUser, ['titre' => 'Offre Parcours', 'poste' => 'Offre Parcours', 'slug' => 'offre-parcours']);

        $response = $this->actingAs($user)->post(route('candidatures.store'), [
            'offre_id' => $offer->id,
            'cv' => UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf'),
        ]);
        $this->step($j, 'POST candidature (avec fichier CV)', $response);
        $response->assertOk();
        $postulation = Postulation::where('user_id', $user->id)->where('offre_id', $offer->id)->first();
        $this->assertNotNull($postulation, 'La candidature devrait être créée');

        $notifEntreprise = Notification::where('user_id', $enterpriseUser->id)->where('role', 'entreprise')->exists();
        $this->step($j, 'notification créée pour l’entreprise ?', $notifEntreprise ? 'oui' : 'non');

        // 8. Suivi : historique, détail, aperçu du CV déposé
        $response = $this->get(route('user.historiques'));
        $this->step($j, 'GET historique des candidatures', $response);
        $response = $this->get(route('user.detail-candidature', ['id' => $postulation->id]));
        $this->step($j, 'GET détail candidature', $response);
        $response = $this->get(route('preview.cv-ia', $postulation->id));
        $this->step($j, 'GET aperçu CV (sa propre candidature)', $response);
        $response = $this->get(route('notifications.index'));
        $this->step($j, 'GET notifications candidat', $response);
    }

    public function test_journey_entreprise_register_to_status_change(): void
    {
        $j = 'entreprise';

        // 1. Inscription entreprise
        $response = $this->post(route('register.entreprise'), [
            'name' => 'Martin',
            'prenom' => 'Claire',
            'email' => 'parcours-entreprise2@example.com',
            'telephone' => '5145551234',
            'adresse' => '123 rue du Parcours',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company_name' => 'Parcours Studio',
            'neq' => 'NEQ-99999',
        ]);
        $this->step($j, 'POST /register.entreprise', $response);
        $user = User::where('email', 'parcours-entreprise2@example.com')->first();
        $this->assertNotNull($user);
        $entreprise = Entreprise::where('user_id', $user->id)->first();
        $this->step($j, 'statut entreprise après inscription', (string) ($entreprise?->status), 'validation admin requise si pending');

        // 2. Vérification du courriel par le lien à jeton personnalisé reçu par courriel
        $token = \App\Models\EmailVerification::where('email', $user->email)->latest('id')->value('token');
        $this->step($j, 'jeton de vérification créé ?', $token ? 'oui' : 'non');
        $response = $this->get('/verify-email/'.$token);
        $this->step($j, 'GET /verify-email/{jeton} (invité)', $response);
        $this->assertNotNull($user->fresh()->email_verified_at, 'Le courriel entreprise devrait être vérifié par le lien à jeton');
        $this->step($j, 'statut entreprise après vérification courriel', (string) $entreprise->fresh()->status);

        // 3. Connexion avant validation admin : l’accès entreprise doit rester fermé
        $response = $this->post('/login', ['email' => 'parcours-entreprise2@example.com', 'password' => 'Password123!']);
        $this->step($j, 'POST /login (avant validation admin)', $response);
        $response = $this->actingAs($user->fresh())->get(route('offres.publies'));
        $this->step($j, 'GET /entreprise avant validation admin', $response);

        // 4. Validation par l’admin via le vrai endpoint de l’interface admin.
        //    On surveille si un courriel « compte activé » part vers l’entreprise.
        Mail::fake();
        $admin = User::factory()->create([
            'prenom' => 'Audit', 'name' => 'Admin', 'email' => 'parcours-admin@example.com',
            'email_verified_at' => now(), 'status' => 'Actif',
        ]);
        $admin->syncRoles(['admin']);
        $response = $this->actingAs($admin)->patch(route('admin.entreprises.status', $entreprise), ['status' => 'approved']);
        $this->step($j, 'PATCH validation admin (approved)', $response);
        $this->assertSame('approved', $entreprise->fresh()->status);
        try {
            Mail::assertSent(\App\Mail\EntrepriseValidated::class);
            $this->step($j, 'courriel « compte activé » envoyé à l’entreprise ?', 'oui');
        } catch (\Throwable $e) {
            $this->step($j, 'courriel « compte activé » envoyé à l’entreprise ?', 'non', 'promis à la vérification du courriel, jamais envoyé par ce chemin');
        }

        // 5. Connexion après validation
        $response = $this->post('/login', ['email' => 'parcours-entreprise2@example.com', 'password' => 'Password123!']);
        $this->step($j, 'POST /login (après validation)', $response);
        $response = $this->actingAs($user->fresh())->get(route('offres.publies'));
        $this->step($j, 'GET /entreprise après validation admin', $response);

        // 3. Publier une offre (sans plan : limite d’une offre active)
        $entrepriseUser = $user;
        $response = $this->actingAs($entrepriseUser)->post(route('offres.store'), $this->validOfferPayload(['jobTitle' => 'Offre Parcours Entreprise']));
        $this->step($j, 'POST publication d’offre', $response);
        $offer = \App\Models\Offre::where('entreprise_id', $entreprise->id)->latest('id')->first();
        $this->assertNotNull($offer, 'L’offre devrait être publiée');
        $this->step($j, 'statut de l’offre publiée', (string) $offer->status);

        // 4. Un candidat postule (parcours HTTP du candidat)
        $candidate = $this->createCandidate(['email' => 'parcours-candidat2@example.com'], false);
        $response = $this->actingAs($candidate)->post(route('candidatures.store'), [
            'offre_id' => $offer->id,
            'cv' => UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf'),
        ]);
        $this->step($j, 'candidat postule (POST)', $response);
        $postulation = Postulation::where('user_id', $candidate->id)->where('offre_id', $offer->id)->first();
        $this->assertNotNull($postulation);

        // 5. L’entreprise consulte les candidatures reçues
        $response = $this->actingAs($entrepriseUser)->get(route('entreprise.offres.candidatures', $offer));
        $this->step($j, 'GET candidatures de l’offre', $response);
        $response = $this->get(route('entreprise.candidatures.show', $postulation));
        $this->step($j, 'GET détail d’une candidature', $response);
        $response = $this->get(route('preview.cv-ia.ep', $postulation->id));
        $this->step($j, 'GET aperçu CV entreprise (CV présent)', $response);
        $response = $this->get(route('previewcv', $postulation->id));
        $this->step($j, 'GET aperçu CV entreprise (route previewcv)', $response);
        $response = $this->get(route('notifications.index'));
        $this->step($j, 'GET notifications (acteur entreprise)', $response);

        // 6. Changement de statut → notification interne + courriel au candidat
        Mail::fake();
        $response = $this->actingAs($entrepriseUser)->put(route('candidature.updateStatus', $postulation->id), ['status' => 'shortlisted']);
        $this->step($j, 'PUT changement de statut (shortlisted)', $response);
        $this->assertSame('shortlisted', $postulation->fresh()->status);
        $notifCandidat = Notification::where('user_id', $candidate->id)->where('role', 'candidat')->exists();
        $this->step($j, 'notification interne créée pour le candidat ?', $notifCandidat ? 'oui' : 'non');
        try {
            Mail::assertSent(ApplicationStatusUpdated::class);
            $this->step($j, 'courriel de statut envoyé au candidat ?', 'oui');
        } catch (\Throwable $e) {
            $this->step($j, 'courriel de statut envoyé au candidat ?', 'non', substr($e->getMessage(), 0, 120));
        }
    }

    public function test_journey_payment_subscription_to_active(): void
    {
        $j = 'paiement';

        config(['stripe.secret' => 'sk_test_fake', 'stripe.webhook_secret' => 'whsec_fake']);
        $gateway = new AuditFakeGateway;
        $this->app->instance(PaymentGateway::class, $gateway);

        $candidate = $this->createCandidate(['email' => 'parcours-paiement@example.com'], false);
        $plan = Abonnement::create([
            'nom' => 'Pro',
            'description' => 'Plan Pro du parcours paiement.',
            'montant' => 49,
            'duree' => 30,
            'actif' => true,
        ]);

        // 1. Souscription (crée l’abonnement « En attente »)
        $response = $this->actingAs($candidate)->post(route('abonnements.souscrire'), ['abonnement_id' => $plan->id]);
        $this->step($j, 'POST souscrire au plan Pro', $response);
        $subscription = UserAbonnement::where('user_id', $candidate->id)->latest('id')->first();
        $this->assertNotNull($subscription);
        $this->step($j, 'statut après souscription', (string) $subscription->status);

        // 2. Checkout (fausse passerelle) : redirection vers Stripe hébergé
        $response = $this->actingAs($candidate)->post(route('paiement.checkout', $subscription));
        $this->step($j, 'POST checkout', $response);
        $response->assertRedirect('https://checkout.stripe.test/pay/cs_audit_123');

        // 3. Retour « succès » (ne doit PAS activer à lui seul)
        $response = $this->get(route('paiement.succes'));
        $this->step($j, 'GET page succès (avant webhook)', $response);
        $this->step($j, 'statut après simple retour succès', (string) $subscription->fresh()->status);

        // 4. Webhook signé (simulé) → activation
        $gateway->nextEvent['user_abonnement_id'] = $subscription->id;
        $response = $this->call('POST', route('stripe.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => 'sig_audit',
        ], json_encode(['id' => 'evt_audit']));
        $this->step($j, 'POST webhook checkout.session.completed', $response);
        $this->assertSame('Actif', $subscription->fresh()->status);

        // 5. L’abonnement actif est visible par le candidat
        $response = $this->actingAs($candidate)->get(route('user.abonnement'));
        $this->step($j, 'GET /user/abonnement après activation', $response);
    }
}
