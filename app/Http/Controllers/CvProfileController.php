<?php

namespace App\Http\Controllers;

use App\Models\CvProfile;
use App\Models\CvFormation;
use App\Models\CvCompetence;
use App\Models\CvExperience;
use App\Models\CvLangue;
use App\Models\CvPerfectionnement;
use App\Models\CvBenevolat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    // ... [Toute la validation et le traitement existant] ...

    DB::beginTransaction();

    try {
        // ... [Tout le code de mise à jour existant] ...

        DB::commit();

        // ============================================
        // 🚀 DÉCLENCHEMENT DE L'AUTO-MATCHING
        // ============================================
        try {
            // Dispatcher le job d'auto-matching
            \App\Jobs\AutoMatchingJob::dispatch($cvProfile->user_id)
               ->delay(now()->addSeconds(5));
            
            \Log::info('Auto-matching Job dispatché après mise à jour profil CV', [
                'user_id' => $cvProfile->user_id,
                'cv_profile_id' => $cvProfile->id,
                'has_formations' => $cvProfile->formations()->count() > 0,
                'has_experiences' => $cvProfile->experiences()->count() > 0
            ]);
            
            $autoMatchingTriggered = true;
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors du dispatch AutoMatchingJob après mise à jour CV', [
                'user_id' => $cvProfile->user_id,
                'cv_profile_id' => $cvProfile->id,
                'error' => $e->getMessage()
            ]);
            
            $autoMatchingTriggered = false;
        }

        return response()->json([
            'success' => true,
            'message' => 'Votre profil CV a été mis à jour avec succès !' . 
                        ($autoMatchingTriggered ? ' Recherche d\'opportunités en cours...' : ''),
            'cv_profile_id' => $cvProfile->id,
            'auto_matching_triggered' => $autoMatchingTriggered
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Erreur lors de la mise à jour du CV:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'cv_profile_id' => $id
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la mise à jour',
            'error' => config('app.debug') ? $e->getMessage() : 'Erreur serveur'
        ], 500);
    }
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
}
