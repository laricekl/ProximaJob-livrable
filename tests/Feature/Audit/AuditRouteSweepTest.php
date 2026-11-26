<?php

namespace Tests\Feature\Audit;

use App\Models\Abonnement;
use App\Models\Entreprise;
use App\Models\Notification;
use App\Models\Offre;
use App\Models\Postulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

/**
 * AUDIT — Passe 1 : sweep des routes GET par rôle.
 *
 * Ce test ne juge rien : il visite chaque route GET avec 4 acteurs
 * (invité, candidat vérifié, entreprise vérifiée, admin) et consigne
 * statut HTTP + redirection dans un fichier JSONL pour analyse.
 * Il ne doit donc « réussir » que si le balayage s'est exécuté.
 */
class AuditRouteSweepTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    private string $resultsFile = '/home/hatch/workspace/goals/proximajob-development/hidden_files/sweep-results.jsonl';

    private User $candidate;

    private User $otherCandidate;

    private User $enterpriseUser;

    private User $adminUser;

    private Offre $offer;

    private Postulation $postulation;

    private Abonnement $plan;

    private Entreprise $entrepriseRecord;

    private function buildFixtures(): void
    {
        $this->candidate = $this->createCandidate(['email' => 'audit-candidat@example.com']);
        $this->otherCandidate = $this->createCandidate(['email' => 'audit-autre@example.com'], false);
        $this->enterpriseUser = $this->createEnterprise(['email' => 'audit-entreprise@example.com']);
        $this->entrepriseRecord = $this->enterpriseUser->entreprise;

        $this->adminUser = User::factory()->create([
            'prenom' => 'Audit',
            'name' => 'Admin',
            'email' => 'audit-admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('audit-pass'),
            'status' => 'Actif',
        ]);
        $this->adminUser->syncRoles(['admin']);
        $this->adminUser = $this->adminUser->fresh();

        $this->offer = $this->createOfferFor($this->enterpriseUser, [
            'titre' => 'Offre Audit',
            'poste' => 'Offre Audit',
            'slug' => 'offre-audit',
        ]);

        $this->postulation = Postulation::create([
            'user_id' => $this->otherCandidate->id,
            'offre_id' => $this->offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        Postulation::create([
            'user_id' => $this->candidate->id,
            'offre_id' => $this->offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        $this->plan = Abonnement::create([
            'nom' => 'Plan Audit',
            'description' => 'Plan pour le sweep.',
            'montant' => 10,
            'duree' => 30,
            'actif' => true,
        ]);

        Notification::create([
            'user_id' => $this->candidate->id,
            'role' => 'candidat',
            'title' => 'Notification audit',
            'message' => 'Message audit',
            'link' => '/user',
            'is_read' => false,
        ]);
    }

    private function paramsFor(string $routeName, array $placeholders): array
    {
        $params = [];

        foreach ($placeholders as $placeholder) {
            $params[$placeholder] = match ($placeholder) {
                'offre' => $this->offer,
                'entreprise' => $this->entrepriseRecord,
                'postulation' => $this->postulation,
                'postulationId' => $this->postulation->id,
                'candidature' => $this->postulation->id,
                'user' => $this->otherCandidate,
                'abonnement' => $this->plan,
                'filename' => 'audit-inexistant.pdf',
                'token' => 'audit-token',
                'provider' => 'google',
                'type' => 'cv',
                'document' => 1,
                'path' => 'audit.txt',
                'hash' => sha1($this->candidate->email),
                'id' => match ($routeName) {
                    'edit.offres' => $this->offer->id,
                    'verification.verify' => $this->candidate->id,
                    default => $this->otherCandidate->id,
                },
                default => 1,
            };
        }

        return $params;
    }

    private function record(array $row): void
    {
        file_put_contents(
            $this->resultsFile,
            json_encode($row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n",
            FILE_APPEND | LOCK_EX
        );
    }

    private function visit(string $actorLabel, string $label, string $url): void
    {
        $row = [
            'actor' => $actorLabel,
            'route' => $label,
            'url' => $url,
            'status' => null,
            'location' => null,
            'error_page' => false,
            'exception' => null,
        ];

        try {
            $response = $this->get($url);
            $row['status'] = $response->getStatusCode();
            $location = $response->headers->get('Location');
            $row['location'] = $location ? parse_url($location, PHP_URL_PATH) : null;
            if ($row['status'] === 200) {
                $content = (string) $response->getContent();
                $row['error_page'] = str_contains($content, 'Server Error') || str_contains($content, 'Whoops!');
            }
        } catch (\Throwable $e) {
            $row['exception'] = get_class($e).': '.substr($e->getMessage(), 0, 180);
        }

        $this->record($row);
    }

    private function sweep(string $actorLabel): void
    {
        $this->buildFixtures();

        if ($actorLabel !== 'invite') {
            $actor = match ($actorLabel) {
                'candidat' => $this->candidate,
                'entreprise' => $this->enterpriseUser,
                'admin' => $this->adminUser,
            };
            $this->actingAs($actor);
        }

        foreach (Route::getRoutes() as $route) {
            if (! in_array('GET', $route->methods())) {
                continue;
            }
            $name = $route->getName();
            if (! $name || str_starts_with($name, 'debugbar.')) {
                continue;
            }

            try {
                $url = route($name, $this->paramsFor($name, $route->parameterNames()));
            } catch (\Throwable $e) {
                $this->record([
                    'actor' => $actorLabel,
                    'route' => $name,
                    'url' => $route->uri(),
                    'status' => 'url_generation_failed',
                    'location' => null,
                    'error_page' => false,
                    'exception' => substr($e->getMessage(), 0, 180),
                ]);
                continue;
            }

            $this->visit($actorLabel, $name, $url);
        }

        // Routes GET sans nom repérées dans le route:list.
        $extraUrls = [
            '/admin/abonnements',
            '/admin/newsletters',
            '/admin/offres',
            '/admin/parametres',
            '/admin/statistiques',
            '/admin/utilisateurs',
            '/notifications/unread-count',
            '/up',
        ];

        foreach ($extraUrls as $extra) {
            $this->visit($actorLabel, '(sans nom) '.$extra, url($extra));
        }
    }

    public function test_sweep_as_guest(): void
    {
        $this->sweep('invite');
        $this->assertFileExists($this->resultsFile);
    }

    public function test_sweep_as_candidate(): void
    {
        $this->sweep('candidat');
        $this->assertFileExists($this->resultsFile);
    }

    public function test_sweep_as_entreprise(): void
    {
        $this->sweep('entreprise');
        $this->assertFileExists($this->resultsFile);
    }

    public function test_sweep_as_admin(): void
    {
        $this->sweep('admin');
        $this->assertFileExists($this->resultsFile);
    }
}
