<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Offre;
use App\Models\TypeOffre;
use App\Models\Categorie;
use App\Models\Postulation;
use App\Models\Sector;
use App\Models\Skill;
use App\Models\Diplome; 
use App\Models\UserAbonnement;  
use App\Models\CvProfile;
use App\Models\Abonnement;   
use App\Models\SiteSetting;  
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)  
    {
        $user = Auth::user();

        $recommendedOffres = Offre::with(['entreprise.user', 'type'])
            ->where('status', 'active')
            ->latest()
            ->take(3)
            ->get();

        $applicationsQuery = Postulation::with(['offre.entreprise', 'offre.type'])
            ->where('user_id', $user->id);

        $dashboardStats = [
            'applications_count' => (clone $applicationsQuery)->count(),
            'interviews_count' => (clone $applicationsQuery)->where('status', 'accepted')->count(),
            'auto_applications_count' => (clone $applicationsQuery)->where('autopostulation', true)->count(),
            'profile_views_count' => $user->unreadNotifications()->count(),
        ];

        $recentApplications = (clone $applicationsQuery)
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard', compact('recommendedOffres', 'dashboardStats', 'recentApplications'));
    }

    public function detailCandidature(Request $request)
    {
        $query = Postulation::with([
            'offre.entreprise',
            'offre.type',
            'user.cvProfile.experiences',
            'user.cvProfile.competences',
            'user.skills',
        ])
            ->where('user_id', Auth::id());

        // Permet de voir une candidature spécifique via ?id=X
        if ($request->has('id')) {
            $postulation = $query->where('id', $request->id)->first();
        } else {
            $postulation = $query->latest()->first();
        }

        if (!$postulation) {
            return redirect()->route('user.historiques')
                ->with('error', 'Aucune candidature trouvée.');
        }

        if ($postulation->autopostulation) {
            Notification::where('user_id', Auth::id())
                ->where('role', 'candidat')
                ->where('type', 'matching')
                ->where('link', route('user.detail-candidature', ['id' => $postulation->id], false))
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('user.detail-candidature', compact('postulation'));
    }

    public function publicProfile()
    {
        $user = Auth::user()->load([
            'cvProfile.formations',
            'cvProfile.competences',
            'cvProfile.experiences',
            'cvProfile.langues',
            'candidateSector.sector',
            'candidateSector.diplome',
            'skills',
            'postulations',
        ]);

        $cvProfile = $user->cvProfile;
        $latestExperience = $cvProfile?->experiences->first();

        $skillLabels = $user->skills
            ->pluck('name')
            ->filter()
            ->values();

        if ($skillLabels->isEmpty() && $cvProfile) {
            $skillLabels = $cvProfile->competences
                ->pluck('description')
                ->filter()
                ->map(fn ($label) => Str::limit(trim($label), 40, ''))
                ->values();
        }

        $experienceYears = $user->experience_years;
        if ($experienceYears === 0 && $cvProfile) {
            $experienceYears = $cvProfile->experiences->count();
        }

        $profileData = [
            'headline' => $latestExperience?->poste
                ?? $user->candidateSector?->sector?->name
                ?? 'Profil en recherche active',
            'location' => $cvProfile?->ville
                ?: ($user->adresse ?: 'À compléter'),
            'phone' => $cvProfile?->telephone
                ?: ($user->telephone ?: 'À compléter'),
            'experience_years' => $experienceYears,
            'skills' => $skillLabels->take(8),
            'skills_count' => $skillLabels->count(),
            'completion_percentage' => $user->profileCompletionPercentage(),
            'applications_count' => $user->postulations->count(),
            'pitch' => $latestExperience?->description
                ?: 'Complétez votre profil et votre CV pour mieux présenter votre parcours aux employeurs.',
            'motivation' => $user->postulations
                ->pluck('cover_letter')
                ->filter()
                ->first()
                ?: '',
            'latest_cv_label' => $user->cv ? basename((string) $user->cv) : null,
        ];

        return view('user.profil-public', compact('user', 'cvProfile', 'profileData'));
    }

    public function historique(Request $request)
    {
        $query = Postulation::with(['offre.entreprise', 'offre'])
            ->where('user_id', Auth::id())
            ->where('autopostulation', false)
            ->orderBy('created_at', 'desc');

        // Filtrage par mot-clé
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('offre', function($q) use ($keyword) {
                $q->where('titre', 'like', "%{$keyword}%")
                  ->orWhereHas('entreprise', function($eq) use ($keyword) {
                      $eq->where('company_name', 'like', "%{$keyword}%");
                  });
            });
        }

        // Filtrage par date
        if ($request->filled('date')) {
            switch ($request->date) {
                case 'Cette semaine':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'Ce mois':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
                case 'Cette année':
                    $query->where('created_at', '>=', now()->startOfYear());
                    break;
            }
        }

        // Filtrage par statut
        if ($request->filled('status') && $request->status !== 'Tous statuts') {
            $statusMap = [
                'Accepté' => 'accepted',
                'Rejeté' => 'rejected',
                'En attente' => 'en_attente'
            ];
            $query->where('status', $statusMap[$request->status] ?? $request->status);
        }

        // Tri
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'Plus ancien':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'Statut':
                    $query->orderBy('status', 'asc');
                    break;
                default: // Plus récent
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }

        $postulations = $query->paginate(10);

        return view("user.historique-candidatures", compact('postulations'));
    }

        public function historique_ia(Request $request)
    {


        $query = Postulation::with(['offre.entreprise', 'offre'])
            ->where('user_id', Auth::id())
            ->where('autopostulation', true)
            ->orderBy('created_at', 'desc');

        // Filtrage par mot-clé
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('offre', function($q) use ($keyword) {
                $q->where('titre', 'like', "%{$keyword}%")
                  ->orWhereHas('entreprise', function($eq) use ($keyword) {
                      $eq->where('company_name', 'like', "%{$keyword}%");
                  });
            });
        }

        // Filtrage par date
        if ($request->filled('date')) {
            switch ($request->date) {
                case 'Cette semaine':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'Ce mois':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
                case 'Cette année':
                    $query->where('created_at', '>=', now()->startOfYear());
                    break;
            }
        }

        // Filtrage par statut
        if ($request->filled('status') && $request->status !== 'Tous statuts') {
            $statusMap = [
                'Accepté' => 'accepted',
                'Rejeté' => 'rejected',
                'En attente' => 'en_attente'
            ];
            $query->where('status', $statusMap[$request->status] ?? $request->status);
        }

        // Tri
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'Plus ancien':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'Statut':
                    $query->orderBy('status', 'asc');
                    break;
                default: // Plus récent
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }

        $postulations = $query->paginate(10);
        $newAiPostulationIds = Notification::where('user_id', Auth::id())
            ->where('role', 'candidat')
            ->where('type', 'matching')
            ->where('is_read', false)
            ->where('link', 'like', '%/user/detail-candidature?id=%')
            ->pluck('link')
            ->map(function ($link) {
                preg_match('/[?&]id=(\d+)/', (string) $link, $matches);

                return isset($matches[1]) ? (int) $matches[1] : null;
            })
            ->filter()
            ->values();

       
        return view("user.historique-candidatures-ia" , compact('postulations', 'newAiPostulationIds'));
    }

    
public function jobdetails($slug)
{
    try {
        $offre = Offre::with([
            'entreprise.user', 
            'type', 
            'categorie',
            'sector',
            'diplomes' => function($query) {
                $query->withPivot('obligatoire');
            },
            'skills.skill' // Ajouter cette ligne pour charger les compétences
        ])->where('slug', $slug)
          ->firstOrFail();     

        $existingPostulation = auth()->check()
            ? Postulation::where('user_id', auth()->id())
                ->where('offre_id', $offre->id)
                ->first()
            : null;
        
        return view('user.details', compact('offre', 'existingPostulation'));
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        abort(404, 'Offre non trouvée');
    }
}
 

    public function candidature()
    {
        return view("user.details");
    }

    public function checkEmail(Request $request)
    {
        $emailExists = \App\Models\User::where('email', $request->email)->exists();
        return response()->json(['exists' => $emailExists]);
    }



    public function abonnement()
    {
        $abonnements = Abonnement::where('actif', true)->get();
        $userAbonnement = UserAbonnement::where('user_id', auth()->id())
            ->where('status', 'Actif')
            ->latest('date_fin')
            ->first();

        return view("user.abonnement", compact('abonnements', 'userAbonnement'));
    }

    public function planabonnement()
    {

         $abonnements = Abonnement::get();
    
        // Récupérer les paramètres existants ou créer un enregistrement par défaut
        $settings = SiteSetting::first();
        
        if (!$settings) {
            $settings = SiteSetting::create([
                'site_nom' => 'Proximalob',
                'email' => 'contact@proximalob.com',
                'timezone' => 'Europe/Paris',
            ]);
        }

        // Liste des fuseaux horaires
        $timezones = [
            'Europe/Paris' => 'Europe/Paris (UTC+1)',
            'UTC' => 'UTC',
            'America/New_York' => 'America/New_York (UTC-5)',
            'Asia/Tokyo' => 'Asia/Tokyo (UTC+9)',
            'Europe/London' => 'Europe/London (UTC+0)',
            'America/Los_Angeles' => 'America/Los_Angeles (UTC-8)',
            'Australia/Sydney' => 'Australia/Sydney (UTC+10)',
        ];

        return view("user.planabonnement", compact('settings', 'timezones', 'abonnements'));

        
    }

 
    /**
     * Souscrire à un abonnement (version simplifiée pour tests)
     */
    public function souscrire(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Créer l'enregistrement dans user_abonnements
            UserAbonnement::create([
                'user_id' => $user->id,
                'abonnement_id' => 2, // ID fixe pour les tests
                'date_debut' => Carbon::now(),
                'date_fin' => Carbon::now()->addDays(30),
                'status' => 'Actif'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Abonnement souscrit avec succès !'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la souscription : ' . $e->getMessage()
            ], 500);
        }
    }

    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    
public function update(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,'.$user->id,
        'telephone' => 'nullable|string|max:20',
        'adresse' => 'nullable|string|max:255',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        
        'sector_id' => 'nullable|exists:sectors,id',
        'diplome_id' => 'nullable|exists:diplomes,id',
        'experience_years' => 'nullable|integer|min:0|max:50',
        'salary_expectation_min' => 'nullable|numeric|min:0',
        'salary_expectation' => 'nullable|numeric|min:0',
        'cv' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
        'skills' => 'nullable|array',
        'skills.*' => 'exists:skills,id',
        'skill_levels' => 'nullable|array',
        'skill_levels.*' => [Rule::in(['débutant', 'intermédiaire', 'avancé', 'expert', 'debutant', 'intermediaire', 'avance'])],
        'skill_years' => 'nullable|array',
        'skill_years.*' => 'integer|min:0|max:50'
    ]);

    try {
        DB::beginTransaction();

        // Variable pour stocker le nom du CV
        $cvFileName = null;

        // Gestion de la photo de profil (reste inchangée)
        if ($request->hasFile('profile_photo')) {
            $uploadPath = public_path('assets/images/user_pdp');
            
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            if ($user->profile_photo_path && $user->profile_photo_path !== 'default.jpg') {
                $oldFilePath = public_path('assets/images/user_pdp/'.$user->profile_photo_path);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }

            $fileName = 'user-'.$user->id.'-'.time().'.'.$request->file('profile_photo')->getClientOriginalExtension();
            $request->file('profile_photo')->move($uploadPath, $fileName);
            $validated['profile_photo_path'] = $fileName;
        }

        // CORRECTION: Stockage du CV dans le dossier public pour accès direct
        if ($request->hasFile('cv')) {
            $uploadPath = public_path('assets/cvs');
            
            // Créer le dossier s'il n'existe pas
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            
            // Supprimer l'ancien CV s'il existe
            if ($user->cv) {
                // Nouveau système (privé)
                if (Str::startsWith($user->cv, 'private/')) {
                    try {
                        Storage::delete($user->cv);
                    } catch (\Exception $e) {
                        Log::warning('Impossible de supprimer l\'ancien CV privé', [
                            'user_id' => $user->id,
                            'old_cv' => $user->cv,
                            'error' => $e->getMessage()
                        ]);
                    }
                } 
                // Ancien système (public) - mais pas les chemins temporaires
                else if (!Str::startsWith($user->cv, '/tmp/')) {
                    $oldcv = Str::startsWith($user->cv, 'assets/cvs/')
                        ? public_path($user->cv)
                        : public_path('assets/cvs/'.$user->cv);
                    if (File::exists($oldcv)) {
                        File::delete($oldcv);
                    }
                }
            }

            // Générer un nom de fichier unique
            $uniqueId = Str::uuid()->toString();
            $extension = $request->file('cv')->getClientOriginalExtension();
            $cvFileName = "cv_user_{$user->id}_{$uniqueId}.{$extension}";
            
            // Déplacer le fichier vers le dossier public
            if (!$request->file('cv')->move($uploadPath, $cvFileName)) {
                throw new \Exception('Impossible de déplacer le fichier CV');
            }
            
            Log::info('CV uploadé avec succès', [
                'user_id' => $user->id,
                'fileName' => $cvFileName,
                'fullPath' => $uploadPath . '/' . $cvFileName
            ]);
        }
        
        // Retirer le fichier CV des données validées pour éviter l'écrasement
        unset($validated['cv']);
        
        // Mise à jour des informations de base de l'utilisateur
        $user->fill($validated);
        
        // Mise à jour du CV séparément
        if ($cvFileName) {
            $user->cv = 'assets/cvs/'.$cvFileName;
        }
        
        // Appliquer salary_expectation avant la sauvegarde
        if ($request->has('salary_expectation')) {
            $user->salary_expectation_min = (int) $request->salary_expectation;
        }

        $user->save();

        // Variable pour suivre si des éléments importants ont changé
        $significantChanges = false;

        // Mise à jour ou création du secteur candidat
        if ($request->has('sector_id') || $request->has('diplome_id') || $request->has('experience_years')) {
            $candidateSectorData = [
                'sector_id' => $request->sector_id,
                'diplome_id' => $request->diplome_id,
                'experience_years' => $request->experience_years
            ];

            if ($user->candidateSector) {
                // Vérifier si des changements significatifs ont eu lieu
                $oldSectorId = $user->candidateSector->sector_id;
                $oldDiplomeId = $user->candidateSector->diplome_id;
                $oldExperience = $user->candidateSector->experience_years;
                
                if ($oldSectorId != $request->sector_id || 
                    $oldDiplomeId != $request->diplome_id || 
                    $oldExperience != $request->experience_years) {
                    $significantChanges = true;
                }
                
                $user->candidateSector->update($candidateSectorData);
            } else {
                $user->candidateSector()->create($candidateSectorData);
                $significantChanges = true;
            }
        }

        // Mise à jour des compétences
        if ($request->has('skills')) {
            $skillsData = [];
            
            foreach ($request->skills as $skillId) {
                $level = $request->skill_levels[$skillId] ?? 'debutant';
                $level = [
                    'débutant' => 'debutant',
                    'intermédiaire' => 'intermediaire',
                    'avancé' => 'avance',
                ][$level] ?? $level;

                $skillsData[$skillId] = [
                    'level' => $level,
                    'years_experience' => $request->skill_years[$skillId] ?? 0,
                    'is_validated' => false
                ];
            }
            
            // Vérifier si les compétences ont changé
            $oldSkillIds = $user->skills()->pluck('skill_id')->toArray();
            $newSkillIds = array_keys($skillsData);
            
            if ($oldSkillIds != $newSkillIds) {
                $significantChanges = true;
            }
            
            // Synchroniser les compétences
            $user->skills()->sync($skillsData);
        } else {
            // Si aucune compétence n'est sélectionnée, supprimer toutes les compétences
            if ($user->skills()->count() > 0) {
                $significantChanges = true;
            }
            $user->skills()->detach();
        }

        DB::commit();

        event(new \App\Events\ProfileUpdated($user));

        try {
            if ($significantChanges) {
                // Utiliser le Job pour traitement en arrière-plan
                \App\Jobs\AutoMatchingJob::dispatch($user->id)
                   ->delay(now()->addSeconds(5));
                
                \Illuminate\Support\Facades\Log::info('Auto-matching Job dispatché après mise à jour profil', [
                    'user_id' => $user->id,
                    'significant_changes' => true
                ]);
            }
        } catch (\Exception $e) {
            // Ne pas faire échouer la mise à jour si le matching échoue
            \Illuminate\Support\Facades\Log::error('Erreur lors du dispatch AutoMatchingJob', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        // Recharger l'utilisateur pour avoir les données les plus récentes
        $user->refresh();

        // CORRECTION: Générer l'URL correcte du CV
        $cvUrl = $user->cv ? asset($user->cv) : null;
        
        Log::info('CV final en base de données', [
            'user_id' => $user->id,
            'cv' => $user->cv,
            'cv_url' => $cvUrl
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès' . ($significantChanges ? ' - Recherche d\'opportunités en cours...' : ''),
            'profile_photo_url' => asset('assets/images/user_pdp/'.($user->profile_photo_path ?? 'default.jpg')),
            'cv_url' => $cvUrl,
            'completion_percentage' => $user->profileCompletionPercentage(),
            'auto_matching_triggered' => $significantChanges
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur mise à jour profil utilisateur', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour du profil',
            'error' => $e->getMessage()
        ], 500);
    }
}

 

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function infoscv ()
    {

        $userId = auth()->id();
    $existingProfile = CvProfile::with([
        'formations',
        'competences', 
        'experiences',
        'langues',
        'perfectionnements',
        'benevolats',
        'cvGeneres'
    ])->where('user_id', $userId)->first();

     
        $diplomes = Diplome::orderBy('nom_diplome')->get();

        return view("user.infos-cv", compact('existingProfile', 'diplomes'));
    }

}
