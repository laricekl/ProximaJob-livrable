<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use App\Models\CvGenere;
use App\Models\CvProfile;
use App\Models\Entreprise;
use App\Models\NewsletterCampagne;
use App\Models\Postulation;
use App\Models\Ressource;
use App\Models\SecuritySetting;
use App\Services\AdminAuditService;
use App\Services\JobMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminOperationsController extends Controller
{
    public function entreprises(Request $request)
    {
        $query = Entreprise::with('user')->withCount(['offres', 'candidatures']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('company_name', 'like', "%{$search}%")
                ->orWhere('neq', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%")));
        }
        if ($request->filled('status')) {
            $query->whereIn('status', Entreprise::statusVariants($request->status));
        }

        $entreprises = $query->latest()->paginate(15)->withQueryString();
        $stats = [
            'total' => Entreprise::count(),
            'pending' => Entreprise::whereIn('status', Entreprise::statusVariants('pending'))->count(),
            'approved' => Entreprise::where('status', 'approved')->count(),
            'featured' => Entreprise::where('is_featured', true)->count(),
        ];

        return view('admin.entreprises', compact('entreprises', 'stats'));
    }

    public function updateEntrepriseStatus(Request $request, $entreprise)
    {
        $entreprise = Entreprise::findOrFail($entreprise);
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'pending', 'rejected'])],
            'reason' => ['nullable', 'string', 'max:1000', Rule::requiredIf($request->status === 'rejected')],
        ]);

        $entreprise->forceFill([
            'status' => $data['status'],
            'verified_at' => $data['status'] === 'approved' ? now() : null,
            'rejection_reason' => $data['status'] === 'rejected' ? $data['reason'] : null,
        ])->save();

        $this->audit('entreprise.status', $entreprise, "Statut de {$entreprise->company_name} modifié : {$data['status']}.");

        return back()->with('success', 'Le statut de l’entreprise a été mis à jour.');
    }

    public function toggleEntrepriseFeatured($entreprise)
    {
        $entreprise = Entreprise::findOrFail($entreprise);
        abort_unless($entreprise->status === 'approved', 422, 'Seule une entreprise approuvée peut être mise en vedette.');
        $entreprise->forceFill(['is_featured' => ! $entreprise->is_featured])->save();
        $this->audit('entreprise.featured', $entreprise, "Visibilité publique de {$entreprise->company_name} modifiée.");

        return back()->with('success', $entreprise->is_featured ? 'Entreprise mise en vedette.' : 'Entreprise retirée des vedettes.');
    }

    public function candidatures(Request $request)
    {
        $query = Postulation::with(['user', 'offre.entreprise']);
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('prenom', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                ->orWhereHas('offre', fn ($o) => $o->where('titre', 'like', "%{$search}%")->orWhere('poste', 'like', "%{$search}%")));
        }
        if ($request->filled('status')) {
            $query->whereIn('status', Postulation::statusVariants($request->status));
        }
        if ($request->filled('source')) {
            $query->where('autopostulation', $request->source === 'ia');
        }

        $candidatures = $query->latest()->paginate(20)->withQueryString();
        $stats = [
            'total' => Postulation::count(),
            'pending' => Postulation::where('status', 'en_attente')->count(),
            'accepted' => Postulation::whereIn('status', Postulation::statusVariants('accepted'))->count(),
            'ia' => Postulation::where('autopostulation', true)->count(),
        ];

        return view('admin.candidatures', compact('candidatures', 'stats'));
    }

    public function updateCandidatureStatus(Request $request, $postulation)
    {
        $postulation = Postulation::findOrFail($postulation);
        $data = $request->validate(['status' => ['required', Rule::in(['en_attente', 'shortlisted', 'contacted', 'interview', 'accepted', 'rejected'])]]);
        $postulation->update($data);
        $this->audit('candidature.status', $postulation, "Candidature #{$postulation->id} passée au statut {$data['status']}.");

        return back()->with('success', 'Le statut de la candidature a été mis à jour.');
    }

    public function showCandidature($postulation)
    {
        $candidature = Postulation::with([
            'user.cvProfile',
            'user.candidateSector.sector',
            'user.skills',
            'offre.entreprise',
            'autresDocs',
        ])->findOrFail($postulation);
        $history = AdminAuditLog::with('admin')
            ->where('subject_type', Postulation::class)
            ->where('subject_id', $candidature->id)
            ->latest()
            ->get();

        return view('admin.candidature-show', compact('candidature', 'history'));
    }

    public function updateCandidatureNotes(Request $request, $postulation)
    {
        $candidature = Postulation::findOrFail($postulation);
        $data = $request->validate(['admin_notes' => ['nullable', 'string', 'max:5000']]);
        $candidature->update($data);
        $this->audit('candidature.notes', $candidature, "Notes internes de la candidature #{$candidature->id} mises à jour.");

        return back()->with('success', 'Notes internes enregistrées.');
    }

    public function candidatureDocument($postulation, string $type)
    {
        $candidature = Postulation::with('user')->findOrFail($postulation);
        $path = match ($type) {
            'cv' => $candidature->cv,
            'motivation' => $candidature->cover_letter ?: $candidature->lettre_motivation,
            default => null,
        };
        abort_unless($path, 404, 'Document introuvable.');

        return $this->serveApplicationDocument($path, $type.'-'.$candidature->id);
    }

    public function candidatureOtherDocument($postulation, $document)
    {
        $candidature = Postulation::findOrFail($postulation);
        $document = $candidature->autresDocs()->findOrFail($document);

        return $this->serveApplicationDocument($document->path, 'document-'.$document->id);
    }

    public function ressources(Request $request)
    {
        $query = Ressource::query();
        if ($request->filled('search')) {
            $query->where('titre', 'like', '%'.$request->search.'%');
        }
        $ressources = $query->latest()->paginate(15)->withQueryString();

        return view('admin.ressources', compact('ressources'));
    }

    public function storeRessource(Request $request)
    {
        $ressource = Ressource::create($this->validateRessource($request));
        $this->audit('ressource.create', $ressource, "Ressource « {$ressource->titre} » créée.");

        return back()->with('success', 'Ressource créée.');
    }

    public function updateRessource(Request $request, $ressource)
    {
        $ressource = Ressource::findOrFail($ressource);
        $ressource->update($this->validateRessource($request));
        $this->audit('ressource.update', $ressource, "Ressource « {$ressource->titre} » modifiée.");

        return back()->with('success', 'Ressource modifiée.');
    }

    public function destroyRessource($ressource)
    {
        $ressource = Ressource::findOrFail($ressource);
        $title = $ressource->titre;
        $id = $ressource->id;
        $ressource->delete();
        $this->audit('ressource.delete', null, "Ressource « {$title} » supprimée.", ['ressource_id' => $id]);

        return back()->with('success', 'Ressource supprimée.');
    }

    public function pilotage()
    {
        $security = SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings());
        $roles = Role::with('permissions')->whereIn('name', ['admin', 'Marketing'])->get();
        $permissions = Permission::orderBy('name')->get()->groupBy(fn ($permission) => str($permission->name)->before('.')->toString());
        $templates = collect(config('cv_templates', []))->map(function ($template, $key) {
            $template['key'] = $key;
            $template['usage'] = CvGenere::where('template_style', $key)->count();

            return $template;
        })->values();
        $auditLogs = AdminAuditLog::with('admin')->latest()->take(25)->get();
        $health = [
            'database' => $this->databaseIsAvailable(),
            'cache' => $this->cacheIsAvailable(),
            'queue_failures' => Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0,
            'environment' => app()->environment(),
        ];
        $cvStats = [
            'profiles' => CvProfile::count(),
            'generated' => CvGenere::count(),
            'scored' => Postulation::whereNotNull('match_score')->count(),
            'outdated' => Postulation::where(fn ($q) => $q->whereNull('algorithm_version')->orWhere('algorithm_version', '!=', '4.0'))->count(),
        ];

        return view('admin.pilotage', compact('security', 'roles', 'permissions', 'templates', 'auditLogs', 'health', 'cvStats'));
    }

    public function updateSecurity(Request $request)
    {
        $data = $request->validate([
            'enable_2fa' => ['nullable', 'boolean'],
            'complex_passwords' => ['nullable', 'boolean'],
            'password_expiration' => ['nullable', 'boolean'],
            'password_expiration_days' => ['nullable', 'integer', 'min:1', 'max:365', Rule::requiredIf($request->boolean('password_expiration'))],
            'brute_force_protection' => ['nullable', 'boolean'],
            'login_attempts_log' => ['nullable', 'boolean'],
            'admin_activities_log' => ['nullable', 'boolean'],
            'log_retention_days' => ['required', 'integer', 'min:1', 'max:365'],
            'admin_ips' => ['nullable', 'string', 'max:4000'],
        ]);
        foreach (['enable_2fa', 'complex_passwords', 'password_expiration', 'brute_force_protection', 'login_attempts_log', 'admin_activities_log'] as $field) {
            $data[$field] = $request->boolean($field);
        }
        $data['admin_ips'] = collect(preg_split('/[\s,]+/', (string) ($data['admin_ips'] ?? '')))
            ->filter()->unique()->values()->all();
        foreach ($data['admin_ips'] as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP)) {
                throw ValidationException::withMessages(['admin_ips' => "L’adresse IP {$ip} n’est pas valide."]);
            }
        }
        if ($data['admin_ips'] !== [] && ! in_array($request->ip(), $data['admin_ips'], true)) {
            throw ValidationException::withMessages([
                'admin_ips' => 'Votre adresse IP actuelle doit rester autorisée pour éviter de bloquer votre accès.',
            ]);
        }
        SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings())->update($data);
        $this->audit('security.update', null, 'Paramètres de sécurité mis à jour.', force: true);

        return back()->with('success', 'Paramètres de sécurité enregistrés.');
    }

    public function updateRole(Request $request, $role)
    {
        $role = Role::findOrFail($role);
        abort_if($role->name === 'admin', 422, 'Les permissions du super administrateur sont protégées.');
        $data = $request->validate(['permissions' => ['array'], 'permissions.*' => ['string', 'exists:permissions,name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        $this->audit('role.permissions', $role, "Permissions du rôle {$role->name} mises à jour.");

        return back()->with('success', 'Permissions du rôle mises à jour.');
    }

    public function recalculateMatching(JobMatchingService $matchingService)
    {
        $count = $matchingService->recalculateStoredApplications();
        $this->audit('matching.recalculate', null, "{$count} scores de compatibilité recalculés.");

        return back()->with('success', "{$count} score(s) recalculé(s) avec l’algorithme 4.0.");
    }

    public function updateNewsletter(Request $request, $campagne)
    {
        $campagne = NewsletterCampagne::findOrFail($campagne);
        abort_unless(in_array($campagne->statut, ['brouillon', 'echec'], true), 422, 'Une campagne déjà envoyée ne peut plus être modifiée.');
        $data = $request->validate([
            'sujet' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string'],
            'audience' => ['required', Rule::in(['tous', 'candidats', 'entreprises', 'premium'])],
        ]);
        $campagne->update($data);
        $this->audit('newsletter.update', $campagne, "Campagne « {$campagne->sujet} » modifiée.");

        return back()->with('success', 'Brouillon de campagne mis à jour.');
    }

    public function destroyNewsletter($campagne)
    {
        $campagne = NewsletterCampagne::findOrFail($campagne);
        abort_unless(in_array($campagne->statut, ['brouillon', 'echec'], true), 422, 'Une campagne déjà envoyée doit être conservée dans l’historique.');
        $subject = $campagne->sujet;
        $id = $campagne->id;
        $campagne->delete();
        $this->audit('newsletter.delete', null, "Campagne « {$subject} » supprimée.", ['campagne_id' => $id]);

        return back()->with('success', 'Campagne supprimée.');
    }

    private function validateRessource(Request $request): array
    {
        return $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['guide', 'modele', 'video', 'lien', 'autre'])],
            'fichier_url' => ['required', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'access' => ['required', Rule::in(['gratuit', 'premium', 'entreprise'])],
        ]);
    }

    private function audit(string $action, $subject, string $description, array $metadata = [], bool $force = false): void
    {
        app(AdminAuditService::class)->record($action, $subject, $description, $metadata, force: $force);
    }

    private function serveApplicationDocument(string $storedPath, string $displayName)
    {
        $urlPath = parse_url($storedPath, PHP_URL_PATH) ?: $storedPath;
        $relative = ltrim(rawurldecode($urlPath), '/');
        $relative = Str::after($relative, 'storage/');
        $candidates = [
            public_path(ltrim(rawurldecode($urlPath), '/')),
            Storage::disk('public')->path($relative),
            Storage::path($relative),
        ];
        $allowedRoots = array_filter([
            realpath(public_path('assets/cvs')),
            realpath(Storage::disk('public')->path('')),
            realpath(Storage::path('')),
        ]);

        $fullPath = collect($candidates)->first(function ($candidate) use ($allowedRoots) {
            if (! File::exists($candidate) || ! ($realPath = realpath($candidate))) {
                return false;
            }

            return collect($allowedRoots)->contains(
                fn ($root) => $realPath === $root || Str::startsWith($realPath, $root.DIRECTORY_SEPARATOR)
            );
        });
        abort_unless($fullPath, 404, 'Document introuvable.');

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$displayName.'.'.$extension.'"',
        ]);
    }

    private function databaseIsAvailable(): bool
    {
        try {
            DB::select('select 1');

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function cacheIsAvailable(): bool
    {
        try {
            Cache::put('admin_health_check', true, 5);

            return Cache::get('admin_health_check') === true;
        } catch (\Throwable) {
            return false;
        }
    }
}
