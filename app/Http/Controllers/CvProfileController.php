<?php

namespace App\Http\Controllers;

use App\Models\CvProfile;
use App\Models\CvFormation;
use App\Models\CvCompetence;
use App\Models\CvExperience;
use App\Models\CvLangue;
use App\Models\CvPerfectionnement;
use App\Models\CvBenevolat;
use App\Models\Diplome;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CvProfileController extends Controller
{
    /**
     * Afficher le formulaire de création de CV
     */
     

    /**
     * Stocker un nouveau profil CV
     */
   /* public function store(Request $request)
    {

            $userId = auth()->id();
            $existingProfile = CvProfile::where('user_id', $userId)->first();
            
            if ($existingProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà un profil CV. Vous pouvez le modifier depuis votre espace.',
                    'profile_id' => $existingProfile->id
                ], 422);
            }
        // Validation des données principales
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:50',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:20',
            'langues_competences' => 'nullable|string',
            'logiciels' => 'nullable|string',
            
            // Validations pour les tableaux
            'formations' => 'nullable|array',
            'formations.*.periode' => 'nullable|string|max:100',
            'formations.*.diplome' => 'required_with:formations|exists:diplomes,id',
            'formations.*.etablissement' => 'nullable|string|max:500',
            
            'competences' => 'nullable|array',
            'competences.*.description' => 'required_with:competences|string',
            
            'experiences' => 'nullable|array',
            'experiences.*.periode' => 'required_with:experiences|string|max:100',
            'experiences.*.poste' => 'required_with:experiences|string|max:500',
            'experiences.*.entreprise' => 'nullable|string|max:500',
            'experiences.*.description' => 'nullable|string',
            
            'langues' => 'nullable|array',
            'langues.*.nom' => 'required_with:langues|string|max:100',
            'langues.*.niveau' => 'nullable|string|in:Langue maternelle,Courant,Intermédiaire,Notions de base,Connaissances de base',
            
            'perfectionnements' => 'nullable|array',
            'perfectionnements.*.annee' => 'nullable|string|max:50',
            'perfectionnements.*.formation' => 'required_with:perfectionnements|string|max:500',
            'perfectionnements.*.etablissement' => 'nullable|string|max:500',
            
            'benevolats' => 'nullable|array',
            'benevolats.*.periode' => 'nullable|string|max:100',
            'benevolats.*.role' => 'required_with:benevolats|string|max:500',
            'benevolats.*.organisation' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez corriger les erreurs du formulaire',
                'errors' => $validator->errors()
            ], 422);
        }

        // Utiliser une transaction pour garantir l'intégrité des données
        DB::beginTransaction();

        try {
            // Extraire la province de la ville si fournie
            $province = $this->extractProvince($request->ville);

            // Créer le profil CV principal
            $cvProfile = CvProfile::create([
                'user_id' => auth()->id(), // Si l'utilisateur est connecté
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'ville' => $request->ville,
                'code_postal' => $request->code_postal,
                'province' => $province,
                'langues_competences' => $request->langues_competences,
                'logiciels' => $request->logiciels,
            ]);

            // Ajouter les formations
            if ($request->has('formations')) {
                foreach ($request->formations as $index => $formation) {
                       
                        if (!empty($formation['diplome'])) {
                            $diplome = \App\Models\Diplome::find($formation['diplome']);
                            if ($diplome) {
                                CvFormation::create([
                                    'cv_profile_id' => $cvProfile->id,
                                    'periode' => $formation['periode'] ?? null,
                                    'diplome' => $diplome->nom_diplome,
                                    'diplome_id' => $diplome->id,
                                    'etablissement' => $formation['etablissement'] ?? null,
                                    'ordre' => $index
                                ]);
                            } else {
                                \Log::warning('Diplôme non trouvé', [
                                    'diplome_id' => $formation['diplome'],
                                    'cv_profile_id' => $cvProfile->id
                                ]);
                            }
                        }
                }
            }

       
            // Ajouter les compétences spécifiques
            if ($request->has('competences')) {
                foreach ($request->competences as $index => $competence) {
                    if (!empty($competence['description'])) {
                        CvCompetence::create([
                            'cv_profile_id' => $cvProfile->id,
                            'description' => $competence['description'],
                            'type' => 'specifique',
                            'ordre' => $index
                        ]);
                    }
                }
            }

            // Ajouter les expériences professionnelles
            if ($request->has('experiences')) {
                foreach ($request->experiences as $index => $experience) {
                    if (!empty($experience['poste'])) {
                        CvExperience::create([
                            'cv_profile_id' => $cvProfile->id,
                            'periode' => $experience['periode'],
                            'poste' => $experience['poste'],
                            'entreprise' => $experience['entreprise'] ?? null,
                            'description' => $experience['description'] ?? null,
                            'ordre' => $index
                        ]);
                    }
                }
            }

            // Ajouter les langues
            if ($request->has('langues')) {
                foreach ($request->langues as $index => $langue) {
                    if (!empty($langue['nom'])) {
                        CvLangue::create([
                            'cv_profile_id' => $cvProfile->id,
                            'nom' => $langue['nom'],
                            'niveau' => $langue['niveau'] ?? null,
                            'ordre' => $index
                        ]);
                    }
                }
            }

            // Ajouter les perfectionnements
            if ($request->has('perfectionnements')) {
                foreach ($request->perfectionnements as $index => $perfectionnement) {
                    if (!empty($perfectionnement['formation'])) {
                        CvPerfectionnement::create([
                            'cv_profile_id' => $cvProfile->id,
                            'annee' => $perfectionnement['annee'] ?? null,
                            'formation' => $perfectionnement['formation'],
                            'etablissement' => $perfectionnement['etablissement'] ?? null,
                            'ordre' => $index
                        ]);
                    }
                }
            }

            // Ajouter les activités bénévoles
            if ($request->has('benevolats')) {
                foreach ($request->benevolats as $index => $benevolat) {
                    if (!empty($benevolat['role'])) {
                        CvBenevolat::create([
                            'cv_profile_id' => $cvProfile->id,
                            'periode' => $benevolat['periode'] ?? null,
                            'role' => $benevolat['role'],
                            'organisation' => $benevolat['organisation'] ?? null,
                            'ordre' => $index
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Votre profil CV a été enregistré avec succès !',
                'cv_profile_id' => $cvProfile->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement',
                'error' => $e->getMessage()
            ], 500);
        }
    }*/

     
    public function inlinePrincipalPdf(Request $request)
    {
        $cvProfile = $this->currentUserCvProfile($request);
        $filename = $this->principalCvFilename($cvProfile);
        $pdf = $this->makePrincipalCvPdf($cvProfile);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    public function downloadPrincipalPdf(Request $request)
    {
        $cvProfile = $this->currentUserCvProfile($request);

        return $this->makePrincipalCvPdf($cvProfile)
            ->download($this->principalCvFilename($cvProfile));
    }

    public function uploadSourceCv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cv' => 'required|file|mimes:pdf,doc,docx,txt|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez choisir un CV PDF, DOC, DOCX ou TXT de 5 Mo maximum.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Session utilisateur introuvable.',
            ], 401);
        }

        $uploadPath = public_path('assets/cvs');

        if (! File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        if ($user->cv && Str::startsWith($user->cv, 'assets/cvs/')) {
            $oldPath = public_path($user->cv);

            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        $file = $request->file('cv');
        $extension = $file->getClientOriginalExtension();
        $filename = 'cv_user_'.$user->id.'_'.Str::uuid().'.'.$extension;
        $file->move($uploadPath, $filename);

        $user->cv = 'assets/cvs/'.$filename;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'CV source téléversé. Vous pouvez maintenant l’analyser.',
            'cv' => $user->cv,
            'filename' => $filename,
            'url' => asset($user->cv),
        ]);
    }

    public function importFromUploadedCv(Request $request)
    {
        $user = $request->user();

        if (! $user || empty($user->cv)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun CV televerse trouve sur votre compte.',
            ], 404);
        }

        $path = $this->resolveUploadedCvPath((string) $user->cv);

        if (! $path || ! File::exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Le fichier CV televerse est introuvable.',
            ], 404);
        }

        $text = $this->extractTextFromCvFile($path);

        if (trim($text) === '') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de lire ce CV. Essayez un PDF texte, DOCX ou TXT.',
            ], 422);
        }

        $fallbackDraft = $this->buildCvDraftFromText($text, $user);
        $aiDraft = $this->buildCvDraftWithAi($text);
        $fields = $aiDraft ? $this->mergeCvDrafts($fallbackDraft, $aiDraft) : $fallbackDraft;
        $source = $aiDraft && $this->cvDraftHasUsefulData($aiDraft) ? 'deepseek' : 'fallback';

        return response()->json([
            'success' => true,
            'message' => $source === 'deepseek'
                ? 'CV analyse par IA. Verifiez les champs avant enregistrement.'
                : 'Informations extraites sans IA. Verifiez les champs avant enregistrement.',
            'fields' => $fields,
            'source' => $source,
        ]);
    }

 public function store(Request $request)
{
    $userId = auth()->id();
    $existingProfile = CvProfile::where('user_id', $userId)->first();
    $isUpdatingExisting = (bool) $existingProfile;

    // Validation des données principales
    $validator = Validator::make($request->all(), [
        'nom' => 'required|string|max:255',
        'prenom_cv' => 'required|string|max:255',
        'email_cv' => 'required|email|max:255',
        'telephone_cv' => 'required|string|max:50',
        'adresse' => 'nullable|string',
        'ville' => 'nullable|string|max:255',
        'code_postal' => 'nullable|string|max:20',
        'langues_competences' => 'nullable|string',
        'logiciels' => 'nullable|string',
        
        // Validations pour les tableaux
        'formations' => 'nullable|array',
        'formations.*.periode' => 'nullable|string|max:100',
        'formations.*.diplome' => 'nullable|exists:diplomes,id',
        'formations.*.etablissement' => 'nullable|string|max:500',
        
        'competences' => 'nullable|array',
        'competences.*.description' => 'nullable|string',
        
        'experiences' => 'nullable|array',
        'experiences.*.periode' => 'nullable|string|max:100',
        'experiences.*.poste' => 'nullable|string|max:500',
        'experiences.*.entreprise' => 'nullable|string|max:500',
        'experiences.*.description' => 'nullable|string',
        
        'langues' => 'nullable|array',
        'langues.*.nom' => 'nullable|string|max:100',
        'langues.*.niveau' => 'nullable|string|in:Langue maternelle,Courant,Intermédiaire,Notions de base,Connaissances de base',
        
        'perfectionnements' => 'nullable|array',
        'perfectionnements.*.annee' => 'nullable|string|max:50',
        'perfectionnements.*.formation' => 'nullable|string|max:500',
        'perfectionnements.*.etablissement' => 'nullable|string|max:500',
        
        'benevolats' => 'nullable|array',
        'benevolats.*.periode' => 'nullable|string|max:100',
        'benevolats.*.role' => 'nullable|string|max:500',
        'benevolats.*.organisation' => 'nullable|string|max:500',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Veuillez corriger les erreurs du formulaire',
            'errors' => $validator->errors()
        ], 422);
    }

    // Utiliser une transaction pour garantir l'intégrité des données
    DB::beginTransaction();

    try {
        // Extraire la province de la ville si fournie
        $province = $this->extractProvince($request->ville);

        $profileData = [
            'nom' => $request->nom,
            'prenom' => $request->prenom_cv,
            'email' => $request->email_cv,
            'telephone' => $request->telephone_cv,
            'adresse' => $request->adresse,
            'ville' => $request->ville,
            'code_postal' => $request->code_postal,
            'province' => $province,
            'langues_competences' => $request->langues_competences,
            'logiciels' => $request->logiciels,
        ];

        if ($isUpdatingExisting) {
            $cvProfile = $existingProfile;
            $cvProfile->update($profileData);

            $cvProfile->formations()->delete();
            $cvProfile->competences()->delete();
            $cvProfile->experiences()->delete();
            $cvProfile->langues()->delete();
            $cvProfile->perfectionnements()->delete();
            $cvProfile->benevolats()->delete();
        } else {
            $cvProfile = CvProfile::create([
                'user_id' => auth()->id(),
            ] + $profileData);
        }

        // Ajouter les formations
        if ($request->has('formations') && is_array($request->formations)) {
            foreach ($request->formations as $index => $formation) {
                if (!empty($formation['diplome'])) {
                    $diplome = \App\Models\Diplome::find($formation['diplome']);
                    
                    if ($diplome) {
                        CvFormation::create([
                            'cv_profile_id' => $cvProfile->id,
                            'periode' => $formation['periode'] ?? null,
                            'diplome' => $diplome->nom_diplome,
                            'diplome_id' => $diplome->id,
                            'etablissement' => $formation['etablissement'] ?? null,
                            'ordre' => $index
                        ]);
                    } else {
                        \Log::warning('Diplôme non trouvé', [
                            'diplome_id' => $formation['diplome'],
                            'cv_profile_id' => $cvProfile->id,
                            'index' => $index
                        ]);
                    }
                }
            }
        }

        // Ajouter les compétences spécifiques
        if ($request->has('competences') && is_array($request->competences)) {
            foreach ($request->competences as $index => $competence) {
                if (!empty($competence['description'])) {
                    CvCompetence::create([
                        'cv_profile_id' => $cvProfile->id,
                        'description' => $competence['description'],
                        'type' => 'specifique',
                        'ordre' => $index
                    ]);
                }
            }
        }

        // Ajouter les expériences professionnelles
        if ($request->has('experiences') && is_array($request->experiences)) {
            foreach ($request->experiences as $index => $experience) {
                if (!empty($experience['poste']) && !empty($experience['periode'])) {
                    CvExperience::create([
                        'cv_profile_id' => $cvProfile->id,
                        'periode' => $experience['periode'],
                        'poste' => $experience['poste'],
                        'entreprise' => $experience['entreprise'] ?? null,
                        'description' => $experience['description'] ?? null,
                        'ordre' => $index
                    ]);
                }
            }
        }

        // Ajouter les langues
        if ($request->has('langues') && is_array($request->langues)) {
            foreach ($request->langues as $index => $langue) {
                if (!empty($langue['nom'])) {
                    CvLangue::create([
                        'cv_profile_id' => $cvProfile->id,
                        'nom' => $langue['nom'],
                        'niveau' => $langue['niveau'] ?? null,
                        'ordre' => $index
                    ]);
                }
            }
        }

        // Ajouter les perfectionnements
        if ($request->has('perfectionnements') && is_array($request->perfectionnements)) {
            foreach ($request->perfectionnements as $index => $perfectionnement) {
                if (!empty($perfectionnement['formation'])) {
                    CvPerfectionnement::create([
                        'cv_profile_id' => $cvProfile->id,
                        'annee' => $perfectionnement['annee'] ?? null,
                        'formation' => $perfectionnement['formation'],
                        'etablissement' => $perfectionnement['etablissement'] ?? null,
                        'ordre' => $index
                    ]);
                }
            }
        }

        // Ajouter les activités bénévoles
        if ($request->has('benevolats') && is_array($request->benevolats)) {
            foreach ($request->benevolats as $index => $benevolat) {
                if (!empty($benevolat['role'])) {
                    CvBenevolat::create([
                        'cv_profile_id' => $cvProfile->id,
                        'periode' => $benevolat['periode'] ?? null,
                        'role' => $benevolat['role'],
                        'organisation' => $benevolat['organisation'] ?? null,
                        'ordre' => $index
                    ]);
                }
            }
        }

        DB::commit();

        // ============================================
        // 🚀 DÉCLENCHEMENT DE L'AUTO-MATCHING
        // ============================================
        try {
            // Dispatcher le job d'auto-matching avec un délai
            \App\Jobs\AutoMatchingJob::dispatch($userId)
               ->delay(now()->addSeconds(5));
            
            \Log::info('Auto-matching Job dispatché après création profil CV', [
                'user_id' => $userId,
                'cv_profile_id' => $cvProfile->id,
                'has_formations' => $cvProfile->formations()->count() > 0,
                'has_experiences' => $cvProfile->experiences()->count() > 0
            ]);
            
            $autoMatchingTriggered = true;
            
        } catch (\Exception $e) {
            // Ne pas faire échouer la création du profil si le matching échoue
            \Log::error('Erreur lors du dispatch AutoMatchingJob après création CV', [
                'user_id' => $userId,
                'cv_profile_id' => $cvProfile->id,
                'error' => $e->getMessage()
            ]);
            
            $autoMatchingTriggered = false;
        }

        return response()->json([
            'success' => true,
            'message' => ($isUpdatingExisting
                        ? 'Votre profil CV a été mis à jour avec succès !'
                        : 'Votre profil CV a été enregistré avec succès !') . 
                        ($autoMatchingTriggered ? ' Recherche d\'opportunités en cours...' : ''),
            'cv_profile_id' => $cvProfile->id,
            'auto_matching_triggered' => $autoMatchingTriggered
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        // Log détaillé de l'erreur
        \Log::error('Erreur lors de l\'enregistrement du CV:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de l\'enregistrement',
            'error' => config('app.debug') ? $e->getMessage() : 'Erreur serveur'
        ], 500);
    }
}
 

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        return redirect()->route('infos.cv');
    }

    /**
     * Mettre à jour un profil CV
     */
public function update(Request $request, $id)
{
    // Vérifier que l'utilisateur est autorisé à modifier ce profil
    $cvProfile = CvProfile::findOrFail($id);
    
    if ($cvProfile->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Vous n\'êtes pas autorisé à modifier ce profil.'
        ], 403);
    }

    return $this->store($request);
}

    /**
     * Extraire la province du champ ville
     */
    private function extractProvince($ville)
    {
        if (preg_match('/\((.*?)\)/', $ville, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function currentUserCvProfile(Request $request): CvProfile
    {
        return CvProfile::with([
            'formations',
            'competences',
            'experiences',
            'langues',
            'perfectionnements',
            'benevolats',
        ])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    private function makePrincipalCvPdf(CvProfile $cvProfile)
    {
        return Pdf::loadView('cv.principal-template', [
            'cvProfile' => $cvProfile,
        ])->setPaper('a4', 'portrait');
    }

    private function principalCvFilename(CvProfile $cvProfile): string
    {
        $name = Str::slug(trim($cvProfile->prenom.' '.$cvProfile->nom)) ?: 'candidat';

        return 'cv-principal-'.$name.'.pdf';
    }

    /**
     * Mettre à jour toutes les relations du profil CV
     */
    private function updateRelations(CvProfile $cvProfile, Request $request)
    {
        // Supprimer les anciennes données
        $cvProfile->formations()->delete();
        $cvProfile->competences()->delete();
        $cvProfile->experiences()->delete();
        $cvProfile->langues()->delete();
        $cvProfile->perfectionnements()->delete();
        $cvProfile->benevolats()->delete();

        // Recréer les relations avec les nouvelles données
        $this->createFormations($cvProfile, $request->formations ?? []);
        $this->createCompetences($cvProfile, $request->competences ?? []);
        $this->createExperiences($cvProfile, $request->experiences ?? []);
        $this->createLangues($cvProfile, $request->langues ?? []);
        $this->createPerfectionnements($cvProfile, $request->perfectionnements ?? []);
        $this->createBenevolats($cvProfile, $request->benevolats ?? []);
    }

    // Méthodes helpers pour créer les relations
    private function createFormations($cvProfile, $formations)
    {
        foreach ($formations as $index => $formation) {
            if (!empty($formation['diplome'])) {
                CvFormation::create([
                    'cv_profile_id' => $cvProfile->id,
                    'periode' => $formation['periode'] ?? null,
                    'diplome' => $formation['diplome'],
                    'etablissement' => $formation['etablissement'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createCompetences($cvProfile, $competences)
    {
        foreach ($competences as $index => $competence) {
            if (!empty($competence['description'])) {
                CvCompetence::create([
                    'cv_profile_id' => $cvProfile->id,
                    'description' => $competence['description'],
                    'type' => 'specifique',
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createExperiences($cvProfile, $experiences)
    {
        foreach ($experiences as $index => $experience) {
            if (!empty($experience['poste'])) {
                CvExperience::create([
                    'cv_profile_id' => $cvProfile->id,
                    'periode' => $experience['periode'],
                    'poste' => $experience['poste'],
                    'entreprise' => $experience['entreprise'] ?? null,
                    'description' => $experience['description'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createLangues($cvProfile, $langues)
    {
        foreach ($langues as $index => $langue) {
            if (!empty($langue['nom'])) {
                CvLangue::create([
                    'cv_profile_id' => $cvProfile->id,
                    'nom' => $langue['nom'],
                    'niveau' => $langue['niveau'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createPerfectionnements($cvProfile, $perfectionnements)
    {
        foreach ($perfectionnements as $index => $perfectionnement) {
            if (!empty($perfectionnement['formation'])) {
                CvPerfectionnement::create([
                    'cv_profile_id' => $cvProfile->id,
                    'annee' => $perfectionnement['annee'] ?? null,
                    'formation' => $perfectionnement['formation'],
                    'etablissement' => $perfectionnement['etablissement'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createBenevolats($cvProfile, $benevolats)
    {
        foreach ($benevolats as $index => $benevolat) {
            if (!empty($benevolat['role'])) {
                CvBenevolat::create([
                    'cv_profile_id' => $cvProfile->id,
                    'periode' => $benevolat['periode'] ?? null,
                    'role' => $benevolat['role'],
                    'organisation' => $benevolat['organisation'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    private function resolveUploadedCvPath(string $storedPath): ?string
    {
        foreach ([public_path($storedPath), storage_path('app/public/'.$storedPath), storage_path('app/'.$storedPath)] as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function extractTextFromCvFile(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'txt' => (string) File::get($path),
            'pdf' => $this->extractTextFromPdf($path),
            'docx' => $this->extractTextFromDocx($path),
            'doc' => $this->extractTextFromDoc($path),
            default => '',
        };
    }

    private function extractTextFromPdf(string $path): string
    {
        $binary = trim((string) shell_exec('command -v pdftotext 2>/dev/null'));

        return $binary === ''
            ? ''
            : (string) shell_exec(escapeshellcmd($binary).' -layout '.escapeshellarg($path).' - 2>/dev/null');
    }

    private function extractTextFromDocx(string $path): string
    {
        $zip = new \ZipArchive();

        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        if ($xml === '') {
            return '';
        }

        $xml = preg_replace('/<\/w:p>/', "\n", $xml) ?? $xml;
        $xml = preg_replace('/<\/w:tab>/', "\t", $xml) ?? $xml;

        return html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function extractTextFromDoc(string $path): string
    {
        $binary = trim((string) shell_exec('command -v textutil 2>/dev/null'));

        return $binary === ''
            ? ''
            : (string) shell_exec(escapeshellcmd($binary).' -convert txt -stdout '.escapeshellarg($path).' 2>/dev/null');
    }

    private function buildCvDraftFromText(string $text, $user): array
    {
        $text = preg_replace("/\r\n|\r/", "\n", $text) ?? $text;
        $lines = collect(explode("\n", $text))
            ->map(fn ($line) => trim(preg_replace('/\s+/', ' ', $line) ?? $line))
            ->filter()
            ->values();

        $email = $this->firstRegex($text, '/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i') ?: (string) $user->email;
        $phone = $this->firstRegex($text, '/(?:\+?1[\s.\-]?)?(?:\(?\d{3}\)?[\s.\-]?)\d{3}[\s.\-]?\d{4}/');
        [$prenom, $nom] = $this->splitName($this->guessNameLine($lines, $email, $phone), $user);

        $skillsText = $this->extractSection($text, ['competences', 'compétences', 'skills'], ['experience', 'expérience', 'formation', 'education', 'langues']);
        $experienceText = $this->extractSection($text, ['experience', 'expérience', 'employment', 'work history'], ['formation', 'education', 'competences', 'compétences', 'langues']);
        $formationText = $this->extractSection($text, ['formation', 'education', 'etudes', 'études'], ['experience', 'expérience', 'competences', 'compétences', 'langues']);
        $languageText = $this->extractSection($text, ['langues', 'languages'], ['experience', 'expérience', 'formation', 'education', 'competences', 'compétences']);

        return [
            'nom' => $nom,
            'prenom_cv' => $prenom,
            'email_cv' => $email,
            'telephone_cv' => $phone,
            'ville' => $this->guessCityLine($lines),
            'langues_competences' => $this->shortText($skillsText ?: $this->extractBulletBlock($text), 500),
            'logiciels' => $this->shortText($this->extractSoftwareLine($text)),
            'competences' => array_values(array_filter([
                $this->shortText($skillsText ?: $this->extractBulletBlock($text), 600),
            ])),
            'langues' => array_values(array_filter([
                [
                    'nom' => $this->guessLanguageName($languageText),
                    'niveau' => $this->guessLanguageLevel($languageText),
                ],
            ], fn ($item) => trim(implode('', $item)) !== '')),
            'experiences' => array_values(array_filter([
                [
                    'periode' => $this->firstRegex($experienceText, '/(?:19|20)\d{2}\s*(?:-|–|à|a|to)\s*(?:present|aujourd.?hui|maintenant|(?:19|20)\d{2})/iu'),
                    'poste' => $this->guessTitleFromSection($experienceText),
                    'entreprise' => '',
                    'description' => $this->shortText($experienceText, 900),
                ],
            ], fn ($item) => trim(implode('', $item)) !== '')),
            'formations' => array_values(array_filter([
                [
                    'periode' => $this->firstRegex($formationText, '/(?:19|20)\d{2}\s*(?:-|–|à|a|to)\s*(?:present|aujourd.?hui|maintenant|(?:19|20)\d{2})/iu'),
                    'etablissement' => $this->guessTitleFromSection($formationText),
                ],
            ], fn ($item) => trim(implode('', $item)) !== '')),
        ];
    }

    private function buildCvDraftWithAi(string $text): ?array
    {
        if (env('CV_AI_PROVIDER', 'deepseek') !== 'deepseek' || ! env('DEEPSEEK_API_KEY')) {
            return null;
        }

        $text = $this->shortText($text, 12000);
        $schema = [
            'nom' => '',
            'prenom_cv' => '',
            'email_cv' => '',
            'telephone_cv' => '',
            'adresse' => '',
            'ville' => '',
            'code_postal' => '',
            'langues_competences' => '',
            'logiciels' => '',
            'competences' => [['description' => '']],
            'experiences' => [['periode' => '', 'poste' => '', 'entreprise' => '', 'description' => '']],
            'formations' => [['periode' => '', 'etablissement' => '', 'diplome_text' => '']],
            'langues' => [['nom' => '', 'niveau' => '']],
            'perfectionnements' => [['annee' => '', 'formation' => '', 'etablissement' => '']],
            'benevolats' => [['periode' => '', 'role' => '', 'organisation' => '']],
        ];

        $prompt = "Tu extrais un CV en JSON strict pour pre-remplir un formulaire Laravel.\n"
            ."Contraintes:\n"
            ."- Reponds uniquement avec du JSON valide, sans markdown.\n"
            ."- N'invente rien. Si une information manque, mets une chaine vide.\n"
            ."- Maximum 5 experiences, 5 formations, 8 competences, 5 langues.\n"
            ."- Les niveaux de langue doivent etre: Langue maternelle, Courant, Intermédiaire, Notions de base, Connaissances de base, ou vide.\n"
            ."- Schema attendu: ".json_encode($schema, JSON_UNESCAPED_UNICODE)."\n\n"
            ."Texte du CV:\n".$text;

        try {
            $response = Http::timeout(45)
                ->withToken(env('DEEPSEEK_API_KEY'))
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
                    'temperature' => 0,
                    'max_tokens' => 3000,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tu es un extracteur de CV. Tu reponds uniquement en JSON valide.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || trim($content) === '') {
                return null;
            }

            $decoded = json_decode($content, true);

            return is_array($decoded) ? $this->normalizeAiCvDraft($decoded) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normalizeAiCvDraft(array $draft): array
    {
        $string = fn ($key, $limit = 500) => $this->shortText((string) ($draft[$key] ?? ''), $limit);
        $list = function (string $key, array $allowed, int $limit = 5) use ($draft) {
            $items = is_array($draft[$key] ?? null) ? $draft[$key] : [];

            return collect($items)
                ->filter(fn ($item) => is_array($item))
                ->take($limit)
                ->map(function ($item) use ($allowed) {
                    $normalized = [];

                    foreach ($allowed as $field => $max) {
                        $normalized[$field] = $this->shortText((string) ($item[$field] ?? ''), $max);
                    }

                    return $normalized;
                })
                ->filter(fn ($item) => trim(implode('', $item)) !== '')
                ->values()
                ->all();
        };

        $normalized = [
            'nom' => $string('nom', 120),
            'prenom_cv' => $string('prenom_cv', 120),
            'email_cv' => $string('email_cv', 160),
            'telephone_cv' => $string('telephone_cv', 60),
            'adresse' => $string('adresse', 255),
            'ville' => $string('ville', 160),
            'code_postal' => $string('code_postal', 30),
            'langues_competences' => $string('langues_competences', 800),
            'logiciels' => $string('logiciels', 500),
            'competences' => $list('competences', ['description' => 500], 8),
            'experiences' => $list('experiences', ['periode' => 100, 'poste' => 180, 'entreprise' => 180, 'description' => 900], 5),
            'formations' => collect($list('formations', ['periode' => 100, 'etablissement' => 220, 'diplome_text' => 220], 5))
                ->map(function ($formation) {
                    $formation['diplome'] = $this->matchDiplomeId((string) ($formation['diplome_text'] ?? ''));

                    return $formation;
                })
                ->all(),
            'langues' => $list('langues', ['nom' => 100, 'niveau' => 80], 5),
            'perfectionnements' => $list('perfectionnements', ['annee' => 50, 'formation' => 220, 'etablissement' => 220], 5),
            'benevolats' => $list('benevolats', ['periode' => 100, 'role' => 220, 'organisation' => 220], 5),
        ];

        return $this->normalizeCvNameFields($normalized);
    }

    private function normalizeCvNameFields(array $draft): array
    {
        $firstName = trim((string) ($draft['prenom_cv'] ?? ''));
        $lastName = trim((string) ($draft['nom'] ?? ''));

        if ($firstName !== '' && $lastName !== '' && str_starts_with(
            $this->normalizeSearchText($lastName),
            $this->normalizeSearchText($firstName).' '
        )) {
            $draft['nom'] = trim(mb_substr($lastName, mb_strlen($firstName)));
        }

        return $draft;
    }

    private function mergeCvDrafts(array $fallback, array $ai): array
    {
        $merged = $fallback;

        foreach ($ai as $key => $value) {
            if (is_array($value)) {
                if (! empty($value)) {
                    $merged[$key] = $value;
                }

                continue;
            }

            if (trim((string) $value) !== '') {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    private function cvDraftHasUsefulData(array $draft): bool
    {
        foreach (['nom', 'prenom_cv', 'email_cv', 'telephone_cv', 'adresse', 'ville', 'langues_competences', 'logiciels'] as $key) {
            if (trim((string) ($draft[$key] ?? '')) !== '') {
                return true;
            }
        }

        foreach (['competences', 'experiences', 'formations', 'langues', 'perfectionnements', 'benevolats'] as $key) {
            if (! empty($draft[$key])) {
                return true;
            }
        }

        return false;
    }

    private function matchDiplomeId(string $label): string
    {
        $normalizedLabel = $this->normalizeSearchText($label);

        if ($normalizedLabel === '') {
            return '';
        }

        $diplomes = Diplome::query()->select('id', 'nom_diplome')->get();

        foreach ($diplomes as $diplome) {
            $normalizedDiplome = $this->normalizeSearchText((string) $diplome->nom_diplome);

            if ($normalizedDiplome !== '' && (
                str_contains($normalizedLabel, $normalizedDiplome)
                || str_contains($normalizedDiplome, $normalizedLabel)
            )) {
                return (string) $diplome->id;
            }
        }

        $aliases = [
            'dec' => ['dec', 'cegep'],
            'baccalaureat' => ['baccalaureat', 'bachelor'],
            'dep' => ['dep', 'professionnelles'],
        ];

        foreach ($aliases as $needle => $words) {
            if (! collect($words)->contains(fn ($word) => str_contains($normalizedLabel, $word))) {
                continue;
            }

            $match = $diplomes->first(fn ($diplome) => str_contains(
                $this->normalizeSearchText((string) $diplome->nom_diplome),
                $needle
            ));

            if ($match) {
                return (string) $match->id;
            }
        }

        return '';
    }

    private function normalizeSearchText(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = mb_strtolower($value);

        return trim((string) preg_replace('/[^a-z0-9]+/', ' ', $value));
    }

    private function firstRegex(string $text, string $pattern): string
    {
        preg_match($pattern, $text, $matches);

        return trim($matches[0] ?? '');
    }

    private function guessNameLine($lines, ?string $email, ?string $phone): string
    {
        foreach ($lines->take(8) as $line) {
            if (($email && str_contains($line, $email)) || ($phone && str_contains($line, $phone))) {
                continue;
            }

            if (preg_match('/@|www\.|linkedin|github|curriculum|resume|cv/i', $line)) {
                continue;
            }

            if (str_word_count($line) >= 2 && mb_strlen($line) <= 60) {
                return $line;
            }
        }

        return '';
    }

    private function splitName(string $nameLine, $user): array
    {
        if ($nameLine === '') {
            return [(string) ($user->prenom ?? ''), (string) ($user->name ?? '')];
        }

        $parts = preg_split('/\s+/', trim($nameLine)) ?: [];
        $prenom = array_shift($parts) ?: (string) ($user->prenom ?? '');
        $nom = trim(implode(' ', $parts)) ?: (string) ($user->name ?? '');

        return [$prenom, $nom];
    }

    private function guessCityLine($lines): string
    {
        foreach ($lines->take(12) as $line) {
            if (preg_match('/\b(Montreal|Montréal|Quebec|Québec|Laval|Longueuil|Gatineau|Sherbrooke|Toronto|Ottawa)\b/i', $line)) {
                return $line;
            }
        }

        return '';
    }

    private function extractSection(string $text, array $starts, array $ends): string
    {
        $startPattern = implode('|', array_map(fn ($item) => preg_quote($item, '/'), $starts));
        $endPattern = implode('|', array_map(fn ($item) => preg_quote($item, '/'), $ends));

        if (! preg_match('/(?:^|\n)\s*('.$startPattern.')\s*:?\s*\n(?P<body>.*?)(?=\n\s*('.$endPattern.')\s*:?\s*\n|$)/isu', $text, $matches)) {
            return '';
        }

        return trim($matches['body'] ?? '');
    }

    private function extractSoftwareLine(string $text): string
    {
        preg_match('/(?:logiciels|outils|technologies|software|tools)\s*:?\s*(.+)/iu', $text, $matches);

        return trim($matches[1] ?? '');
    }

    private function extractBulletBlock(string $text): string
    {
        preg_match_all('/^\s*(?:[-*•]|–)\s*(.+)$/mu', $text, $matches);

        return implode("\n", array_slice($matches[1] ?? [], 0, 8));
    }

    private function guessTitleFromSection(string $section): string
    {
        foreach (explode("\n", $section) as $line) {
            $line = trim($line);

            if ($line !== '' && mb_strlen($line) <= 100 && ! preg_match('/^(?:[-*•]|–)/', $line)) {
                return $line;
            }
        }

        return '';
    }

    private function guessLanguageName(string $section): string
    {
        if ($section === '') {
            return '';
        }

        foreach (['Français', 'Francais', 'Anglais', 'English', 'Espagnol', 'Spanish'] as $language) {
            if (stripos($section, $language) !== false) {
                return str_replace('Francais', 'Français', $language);
            }
        }

        return trim(strtok($section, "\n,;")) ?: '';
    }

    private function guessLanguageLevel(string $section): string
    {
        return match (true) {
            preg_match('/maternelle|native/iu', $section) === 1 => 'Langue maternelle',
            preg_match('/courant|fluent|bilingue/iu', $section) === 1 => 'Courant',
            preg_match('/intermediaire|intermédiaire|intermediate/iu', $section) === 1 => 'Intermédiaire',
            preg_match('/base|notions|basic/iu', $section) === 1 => 'Notions de base',
            default => '',
        };
    }

    private function shortText(string $text, int $limit = 500): string
    {
        $text = trim(preg_replace('/\n{3,}/', "\n\n", $text) ?? $text);

        return mb_strlen($text) > $limit ? mb_substr($text, 0, $limit - 1).'…' : $text;
    }
}
