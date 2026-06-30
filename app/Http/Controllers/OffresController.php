<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Offre;
use App\Models\TypeOffre;
use App\Models\Categorie;   
use App\Models\JobOfferSkill;
use App\Models\Sector;
use App\Models\Diplome;

class OffresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
           
        $sort = $request->get('sort', 'random');
        $search = $request->get('search');
        $localisation = $request->get('localisation');
        $categories = array_filter((array) $request->get('categories', [])); // Récupérer les catégories sélectionnées
        $types = array_filter((array) $request->get('type', [])); // Récupérer les types sélectionnés
        $remoteWork = array_filter((array) $request->get('remote_work', []));
        $experience = array_filter((array) $request->get('experience', []));
        
        // Nouveau: Récupérer les paramètres de salaire
        $salaire_min = $request->get('salaire_min');
        $salaire_max = $request->get('salaire_max');
                
        $query = Offre::with([
            'entreprise.user',
            'type',
            'categorie',
            'diplomes' => function($query) {
                $query->withPivot('obligatoire');
            }
        ])->where('status' , 'active');
            
        // Appliquer la recherche par titre de poste
        if ($search) {
            $keywords = explode(' ', $search); // Sépare les mots-clés
            
            $query->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('poste', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('description', 'LIKE', '%'.$keyword.'%')
                    ->orWhereHas('entreprise', function($q) use ($keyword) {
                        $q->where('company_name', 'LIKE', '%'.$keyword.'%');
                    });
                }
            });
        }
            
        // Appliquer le filtre par localisation
        if ($localisation) {
            $query->where('localisation', 'LIKE', '%'.$localisation.'%');
        }
            
        // Appliquer le filtre par catégories
        if (!empty($categories)) {
            $query->whereIn('categorie_id', $categories);
        }
            
        // Appliquer le filtre par type
        if (!empty($types)) {
            $query->whereIn('type_id', $types);
        }

        if (!empty($remoteWork)) {
            $query->where(function($q) use ($remoteWork) {
                foreach ($remoteWork as $mode) {
                    $q->orWhere('remote_work', 'LIKE', '%'.$mode.'%');
                }
            });
        }

        if (!empty($experience)) {
            $query->where(function($q) use ($experience) {
                foreach ($experience as $level) {
                    $q->orWhere('experience', 'LIKE', '%'.$level.'%')
                      ->orWhere('annee_experience', 'LIKE', '%'.$level.'%')
                      ->orWhere('required_experience', 'LIKE', '%'.$level.'%');
                }
            });
        }
        
        // Nouveau: Appliquer le filtre par salaire
        if ($salaire_min !== null && $salaire_min !== '') {
            $query->where('salaire_max', '>=', $salaire_min);
        }
        
        if ($salaire_max !== null && $salaire_max !== '') {
            $query->where('salaire_min', '<=', $salaire_max);
        }
            
        // Appliquer le tri selon la sélection  
        switch($sort) {
            case 'date':
                $query->latest('created_at');
                break;
            case 'salary_asc':
            case 'salaire_asc':
                $query->orderBy('salaire_min', 'asc');
                break;
            case 'salary_desc':
            case 'salaire_desc':
                $query->orderBy('salaire_max', 'desc');
                break;
            case 'random':
                $query->inRandomOrder();
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }
            
        $offres = $query->paginate(5);
            
        // Récupérer toutes les localisations uniques pour le select
        $localisations = Offre::select('localisation')
                            ->distinct()
                            ->whereNotNull('localisation')
                            ->where('localisation', '!=', '')
                            ->orderBy('localisation')
                            ->pluck('localisation');
            
        // Récupérer toutes les catégories avec le nombre d'offres pour chacune
        $categoriesWithCount = Categorie::withCount('offres')
                                    ->orderBy('nom')
                                    ->get();
            
        // Récupérer tous les types d'emploi avec le nombre d'offres pour chacun
        $typesWithCount = TypeOffre::withCount(['offres' => function($query) {
                                // Spécifier explicitement la clé étrangère
                            }])
                                ->orderBy('nom')
                                ->get();
        
        // Nouveau: Récupérer les valeurs min/max de salaire pour le slider
        $salaireRange = Offre::selectRaw('MIN(salaire_min) as min_salaire, MAX(salaire_max) as max_salaire')
                            ->first();
            
        // Maintenir les paramètres dans la pagination  
        $offres->appends(request()->query());
            
        return view("offres.listeoffres", compact(
            'offres', 
            'sort', 
            'search', 
            'localisation', 
            'localisations', 
            'categoriesWithCount',
            'categories',
            'typesWithCount',
            'types',
            'remoteWork',
            'experience',
            'salaire_min',
            'salaire_max',
            'salaireRange'
        ));
   
       
    }
    public function details()
    {
        return redirect()->route('offres');
    }
    public function appform(){
        return redirect()->route('offres');

    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contractTypes = TypeOffre::orderBy('nom')->get();
        $this->ensureDefaultSectors();
        $sectors = Sector::active()->orderBy('name')->get();
        $diplomes = Diplome::actif()->orderBy('nom_diplome')->get();

        return view('entreprise.create-offre', compact('contractTypes', 'sectors', 'diplomes'));
    }

    private function ensureDefaultSectors(): void
    {
        if (Sector::active()->exists()) {
            return;
        }

        $defaultSectors = [
            ['name' => 'Technologie et informatique', 'slug' => 'technologie-et-informatique'],
            ['name' => 'Marketing et communication', 'slug' => 'marketing-et-communication'],
            ['name' => 'Finance et comptabilite', 'slug' => 'finance-et-comptabilite'],
            ['name' => 'Ressources humaines', 'slug' => 'ressources-humaines'],
            ['name' => 'Sante et services sociaux', 'slug' => 'sante-et-services-sociaux'],
        ];

        foreach ($defaultSectors as $sector) {
            Sector::firstOrCreate(
                ['slug' => $sector['slug']],
                [
                    'name' => $sector['name'],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
 
 /*   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jobTitle' => 'required|string|max:255',
            'contractType' => 'required|exists:types_offres,id',
            'location' => 'required|string|max:255',
            'department' => 'required|exists:categories,id',
            'sector' => 'required|exists:sectors,id',  
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'endDate' => 'required|date',
            'diplome' => 'required|numeric|min:1',
            'skills' => 'required|array',  
            'skills.*' => 'exists:skills,id',  
            'languages' => 'required|string',
            'experience' => 'nullable|string',
            'otherCriteria' => 'nullable|string',
            'jobDescription' => 'required|string',
            'jobObjectives' => 'nullable|string',
            'benefits' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Convertir les salaires de format français vers format numérique
            $salaire_min = null;
            $salaire_max = null;
            
            if ($request->salary_min) {
                $salaire_min = floatval(str_replace(',', '.', $request->salary_min));
            }
            
            if ($request->salary_max) {
                $salaire_max = floatval(str_replace(',', '.', $request->salary_max));
            }

            $offre = Offre::create([
                'entreprise_id' => Auth::user()->entreprise->id,
                'titre' => $request->jobTitle,
                'poste' => $request->jobTitle,
                'description' => $request->jobDescription,
                'localisation' => $request->location,
                'categorie_id' => $request->department,
                'sector_id' => $request->sector,  
                'type_id' => $request->contractType,
                'status' => 'active',
                'salaire_min' => $salaire_min,
                'salaire_max' => $salaire_max,
                'date_fin' => $request->endDate,
                'diplome_id' => $request->diplome,
                'competences' => implode(', ', $request->skills),  
                'langues' => $request->languages,
                'annee_experience' => $request->experience,
                'criteres' => $request->otherCriteria,
                'missions' => $request->jobDescription,
                'objectif' => $request->jobObjectives,
                'avantages' => $request->benefits
            ]);

            if ($request->has('skills') && is_array($request->skills)) {
                foreach ($request->skills as $skillId) {
                    JobOfferSkill::create([
                        'job_offer_id' => $offre->id,
                        'skill_id' => $skillId,
                        'is_required' => true,
                        'weight' => 5,  
                    ]);
                }
            }

            //  Déclencher l'auto-matching après création de l'offre
            try {
                \App\Jobs\AutoMatchingJob::dispatch(Auth::user()->id)
                ->delay(now()->addSeconds(10));  
                
                \Illuminate\Support\Facades\Log::info('Auto-matching Job dispatché après création d\'offre', [
                    'offre_id' => $offre->id,
                    'entreprise_id' => Auth::user()->entreprise->id,
                    'triggered_by_user_id' => Auth::user()->id
                ]);
                
            } catch (\Exception $e) {
                // Ne pas faire échouer la création si le matching échoue
                \Illuminate\Support\Facades\Log::error('Erreur lors du dispatch AutoMatchingJob pour nouvelle offre', [
                    'offre_id' => $offre->id,
                    'error' => $e->getMessage()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Offre créée avec succès - Recherche de candidats en cours...',
                'offre' => $offre->load(['type', 'categorie', 'sector'])  
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'offre: ' . $e->getMessage()
            ], 500);
        }
    }
*/


 public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        // Détails du poste
        'jobTitle' => 'required|string|max:255',
        'contractType' => 'required|exists:types_offres,id',
        'location' => 'required|string|max:255',
        'sector' => 'required|exists:sectors,id',
       // 'employment_type' => 'required|string',
        'remote_work' => ['required', 'string', Rule::in(['Présentiel', 'Hybride', 'Télétravail'])],
        'job_category' => ['required', 'string', Rule::in(['informatique', 'marketing', 'finance', 'rh', 'sante'])],
        'salary_type' => ['required', 'string', Rule::in(['annuel', 'mensuel', 'journalier', 'horaire'])],
        'salary_min' => 'nullable|numeric|min:0',
        'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
        'endDate' => 'required|date|after_or_equal:today',
        'start_date' => ['required', 'string', Rule::in(['Immédiate', 'Sous 2 semaines', 'Sous 1 mois', 'Autre'])],
        'start_date_other' => 'required_if:start_date,Autre|nullable|string|max:255',
        
        // Exigences des candidats
        'diplomes' => 'nullable|array',
        'diplomes.*.id' => 'nullable|exists:diplomes,id',
        'diplomes.*.obligatoire' => 'nullable|boolean',
        'skillls' => 'nullable|array',
        'skillls.*' => 'exists:skills,id',
        'methodological_skills' => 'nullable|array',
        'methodological_skills.*' => 'exists:skills,id',
        'num_skills' => 'nullable|array',
        'num_skills.*' => 'exists:skills,id',
        'languages_data' => 'required|string|max:255',
        'required_experience' => ['required', 'string', Rule::in(['Non exigée', '0-1 an', '2-3 ans', '4-5 ans', '5 ans et plus'])],
        'education_level' => 'nullable|string|max:255',
        'otherCriteria' => 'nullable|string|max:2000',
        
        // Description de l'emploi
        'benefits' => 'nullable|array',
        'benefits.*' => 'string|max:255',
        'custom_benefits' => 'nullable|json',
        'responsibilities' => 'required|string|max:10000',
        'jobDescription' => 'required|string|max:10000',
    ]);

    if ($validator->fails()) {
        if (!$request->expectsJson()) {
            return back()->withErrors($validator)->withInput();
        }

        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Convertir les salaires de format français vers format numérique
        $salaire_min = null;
        $salaire_max = null;
        
        if ($request->salary_min) {
            $salaire_min = floatval(str_replace(',', '.', $request->salary_min));
        }
        
        if ($request->salary_max) {
            $salaire_max = floatval(str_replace(',', '.', $request->salary_max));
        }

        // Gérer la date d'entrée
        $start_date = $request->start_date;
        if ($start_date === 'Autre' && $request->start_date_other) {
            $start_date = $request->start_date_other;
        }

        // Combiner tous les avantages (standard + personnalisés)
        $all_benefits = [];
        if ($request->has('benefits')) {
            $all_benefits = array_merge($all_benefits, $request->benefits);
        }
        if ($request->custom_benefits) {
            $custom_benefits = json_decode($request->custom_benefits, true);
            if (is_array($custom_benefits)) {
                $all_benefits = array_merge($all_benefits, $custom_benefits);
            }
        }

        // Créer l'offre
        $offre = Offre::create([
            'entreprise_id' => Auth::user()->entreprise->id,
            'titre' => $request->jobTitle,
            'poste' => $request->jobTitle,
            'description' => $request->jobDescription,
            'localisation' => $request->location,
            'sector_id' => $request->sector,
            'type_id' => $request->contractType,
            'status' => 'active',
            
             
            'employment_type' => $request->employment_type,
            'remote_work' => $request->remote_work,
            'job_category' => $request->job_category,
            'salary_type' => $request->salary_type,
            'salaire_min' => $salaire_min,
            'salaire_max' => $salaire_max,
            'date_fin' => $request->endDate,
            'start_date' => $start_date,
            
            // Exigences
            'langues' => $request->languages_data,
            'required_experience' => $request->required_experience,
            'education_level' => $request->education_level,
            'annee_experience' => $request->required_experience,
            'criteres' => $request->otherCriteria,
            
            // Description
            'missions' => $request->jobDescription,
            'avantages' => json_encode($all_benefits),
            'responsibilities' => $request->responsibilities,
        ]);

        // Attacher les diplômes avec le champ 'obligatoire'
        if ($request->has('diplomes') && is_array($request->diplomes)) {
            $diplomesData = [];
            foreach ($request->diplomes as $diplome) {
                if (empty($diplome['id'])) {
                    continue;
                }

                $diplomesData[$diplome['id']] = [
                    'obligatoire' => (bool) ($diplome['obligatoire'] ?? true)
                ];
            }

            if (!empty($diplomesData)) {
                $offre->diplomes()->attach($diplomesData);
            }
        }

        // Attacher toutes les compétences (techniques, méthodologiques, numériques)
        $allSkills = [];
        
        // Compétences techniques
        if ($request->has('skillls') && is_array($request->skillls)) {
            foreach ($request->skillls as $skillId) {
                $allSkills[] = [
                    'job_offer_id' => $offre->id,
                    'skill_id' => $skillId,
                    'skill_type' => 'technical',
                    'is_required' => true,
                    'weight' => 5,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        // Compétences méthodologiques
        if ($request->has('methodological_skills') && is_array($request->methodological_skills)) {
            foreach ($request->methodological_skills as $skillId) {
                $allSkills[] = [
                    'job_offer_id' => $offre->id,
                    'skill_id' => $skillId,
                    'skill_type' => 'methodological',
                    'is_required' => true,
                    'weight' => 4,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        // Compétences numériques
        if ($request->has('num_skills') && is_array($request->num_skills)) {
            foreach ($request->num_skills as $skillId) {
                $allSkills[] = [
                    'job_offer_id' => $offre->id,
                    'skill_id' => $skillId,
                    'skill_type' => 'digital',
                    'is_required' => true,
                    'weight' => 4,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        if (!empty($allSkills)) {
            JobOfferSkill::insert($allSkills);
        }

        // Déclencher l'auto-matching après création de l'offre
        try {
            \App\Jobs\AutoMatchingJob::dispatch(Auth::user()->id)
                ->delay(now()->addSeconds(10));
            
            \Illuminate\Support\Facades\Log::info('Auto-matching Job dispatché après création d\'offre', [
                'offre_id' => $offre->id,
                'entreprise_id' => Auth::user()->entreprise->id,
                'triggered_by_user_id' => Auth::user()->id
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors du dispatch AutoMatchingJob pour nouvelle offre', [
                'offre_id' => $offre->id,
                'error' => $e->getMessage()
            ]);
        }

        if (!$request->expectsJson()) {
            return redirect()
                ->route('offres.publies')
                ->with('success', 'Offre créée avec succès. Le matching IA va maintenant démarrer.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Offre créée avec succès - Recherche de candidats en cours...',
            'offre' => $offre->load(['type', 'sector', 'diplomes'])
        ]);

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Erreur lors de la création de l\'offre', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        if (!$request->expectsJson()) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'offre: ' . $e->getMessage());
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création de l\'offre: ' . $e->getMessage()
        ], 500);
    }
}



/* public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'jobTitle' => 'required|string|max:255',
        'contractType' => 'required|exists:types_offres,id',
        'location' => 'required|string|max:255',
        'department' => 'required|exists:categories,id',
        'salary_min' => 'nullable|numeric|min:0',
        'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
        'startDate' => 'required|date',
        'diplome' => 'required|numeric|min:1',
        'technicalSkills' => 'required|string',
        'languages' => 'required|string',
        'experience' => 'nullable|string',
        'otherCriteria' => 'nullable|string',
        'jobDescription' => 'required|string',
        'jobObjectives' => 'nullable|string',
        'benefits' => 'nullable|string'
    ]);

    // Ajouter des messages personnalisés pour les erreurs de validation numérique
    $validator->after(function ($validator) use ($request) {
        // Vérifier le format de salary_min
        if ($request->salary_min && !preg_match('/^\d+([,.]\d+)?$/', $request->salary_min)) {
            $validator->errors()->add('salary_min', 'Le format du salaire minimum est invalide. Utilisez le format 35000 ou 35000,50');
        }
        
        // Vérifier le format de salary_max
        if ($request->salary_max && !preg_match('/^\d+([,.]\d+)?$/', $request->salary_max)) {
            $validator->errors()->add('salary_max', 'Le format du salaire maximum est invalide. Utilisez le format 45000 ou 45000,50');
        }
    });

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Trouver l'offre à modifier
        $offre = Offre::where('id', $id)
                      ->where('entreprise_id', Auth::user()->entreprise->id)
                      ->firstOrFail();

         
        $salaire_min = null;
        $salaire_max = null;  
        
        if ($request->salary_min) {
            // Remplacer la virgule par un point et convertir en float
            $salaire_min = floatval(str_replace(',', '.', str_replace(' ', '', $request->salary_min)));
        }
        
        if ($request->salary_max) {
            // Remplacer la virgule par un point et convertir en float
            $salaire_max = floatval(str_replace(',', '.', str_replace(' ', '', $request->salary_max)));
        }

        // Mettre à jour l'offre
        $offre->update([
            'titre' => $request->jobTitle,
            'poste' => $request->jobTitle,
            'description' => $request->jobDescription,
            'localisation' => $request->location,
            'categorie_id' => $request->department,
            'type_id' => $request->contractType,
            'salaire_min' => $salaire_min,
            'salaire_max' => $salaire_max,
            'date_debut' => $request->startDate,
            'diplomes' => $request->diplome,
            'competences' => $request->technicalSkills,
            'langues' => $request->languages,
            'annee_experience' => $request->experience,
            'criteres' => $request->otherCriteria,
            'missions' => $request->jobDescription,
            'objectif' => $request->jobObjectives,
            'avantages' => $request->benefits
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Offre modifiée avec succès',
            'offre' => $offre->load(['type', 'categorie'])
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Offre non trouvée ou vous n\'avez pas les droits pour la modifier'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la modification de l\'offre: ' . $e->getMessage()
        ], 500);
    }
}*/

 public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        // Détails du poste
        'jobTitle' => 'required|string|max:255',
        'contractType' => 'required|exists:types_offres,id',
        'location' => 'required|string|max:255',
        'sector' => 'required|exists:sectors,id',
        'employment_type' => 'nullable|string',
        'remote_work' => ['required', 'string', Rule::in(['Présentiel', 'Hybride', 'Télétravail'])],
        'job_category' => ['required', 'string', Rule::in(['informatique', 'marketing', 'finance', 'rh', 'sante'])],
        'salary_type' => ['required', 'string', Rule::in(['annuel', 'mensuel', 'journalier', 'horaire'])],
        'salary_min' => 'nullable|numeric|min:0',
        'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
        'endDate' => 'required|date|after_or_equal:today',
        'start_date' => ['required', 'string', Rule::in(['Immédiate', 'Sous 2 semaines', 'Sous 1 mois', 'Autre'])],
        'start_date_other' => 'required_if:start_date,Autre|nullable|string|max:255',
        
        // Exigences des candidats
        'diplomes' => 'nullable|array',
        'diplomes.*.id' => 'required_with:diplomes|exists:diplomes,id',
        'diplomes.*.obligatoire' => 'required_with:diplomes|boolean',
        'skillls' => 'nullable|array',
        'skillls.*' => 'exists:skills,id',
        'methodological_skills' => 'nullable|array',
        'methodological_skills.*' => 'exists:skills,id',
        'num_skills' => 'nullable|array',
        'num_skills.*' => 'exists:skills,id',
        'languages_data' => 'required|string|max:255',
        'required_experience' => ['required', 'string', Rule::in(['Non exigée', '0-1 an', '2-3 ans', '4-5 ans', '5 ans et plus'])],
        'education_level' => 'nullable|string|max:255',
        'otherCriteria' => 'nullable|string|max:2000',
        
        // Description de l'emploi
        'benefits' => 'nullable|array',
        'benefits.*' => 'string|max:255',
        'custom_benefits' => 'nullable|json',
        'responsibilities' => 'required|string|max:10000',
        'jobDescription' => 'required|string|max:10000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Trouver l'offre
        $offre = Offre::findOrFail($id);
        
        // Vérifier que l'offre appartient bien à l'entreprise connectée
        if ($offre->entreprise_id !== Auth::user()->entreprise->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à modifier cette offre.'
            ], 403);
        }

        // Convertir les salaires
        $salaire_min = null;
        $salaire_max = null;
        
        if ($request->salary_min) {
            $salaire_min = floatval(str_replace(',', '.', $request->salary_min));
        }
        
        if ($request->salary_max) {
            $salaire_max = floatval(str_replace(',', '.', $request->salary_max));
        }

        // Gérer la date d'entrée
        $start_date = $request->start_date;
        if ($start_date === 'Autre' && $request->start_date_other) {
            $start_date = $request->start_date_other;
        }

        // Combiner tous les avantages
        $all_benefits = [];
        if ($request->has('benefits')) {
            $all_benefits = array_merge($all_benefits, $request->benefits);
        }
        if ($request->custom_benefits) {
            $custom_benefits = json_decode($request->custom_benefits, true);
            if (is_array($custom_benefits)) {
                $all_benefits = array_merge($all_benefits, $custom_benefits);
            }
        }

        // Mettre à jour l'offre
        $offre->update([
            'titre' => $request->jobTitle,
            'poste' => $request->jobTitle,
            'description' => $request->jobDescription,
            'localisation' => $request->location,
            'sector_id' => $request->sector,
            'type_id' => $request->contractType,
            
            // Nouveaux champs
            'employment_type' => $request->employment_type,
            'remote_work' => $request->remote_work,
            'job_category' => $request->job_category,
            'salary_type' => $request->salary_type,
            'salaire_min' => $salaire_min,
            'salaire_max' => $salaire_max,
            'date_fin' => $request->endDate,
            'start_date' => $start_date,
            
            // Exigences
            'langues' => $request->languages_data,
            'required_experience' => $request->required_experience,
            'education_level' => $request->education_level,
            'annee_experience' => $request->required_experience,
            'criteres' => $request->otherCriteria,
            
            // Description
            'missions' => $request->jobDescription,
            'avantages' => json_encode($all_benefits),
            'responsibilities' => $request->responsibilities,
        ]);

        // Mettre à jour les diplômes
        $offre->diplomes()->detach(); // Détacher tous les diplômes existants
        
        if ($request->has('diplomes') && is_array($request->diplomes)) {
            $diplomesData = [];
            foreach ($request->diplomes as $diplome) {
                $diplomesData[$diplome['id']] = [
                    'obligatoire' => $diplome['obligatoire']
                ];
            }
            $offre->diplomes()->attach($diplomesData);
        }

        // Mettre à jour les compétences
        // Supprimer toutes les anciennes compétences
        JobOfferSkill::where('job_offer_id', $offre->id)->delete();
        
        $allSkills = [];
        
        // Compétences techniques
        if ($request->has('skillls') && is_array($request->skillls)) {
            foreach ($request->skillls as $skillId) {
                $allSkills[] = [
                    'job_offer_id' => $offre->id,
                    'skill_id' => $skillId,
                    'skill_type' => 'technical',
                    'is_required' => true,
                    'weight' => 5,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        // Compétences méthodologiques
        if ($request->has('methodological_skills') && is_array($request->methodological_skills)) {
            foreach ($request->methodological_skills as $skillId) {
                $allSkills[] = [
                    'job_offer_id' => $offre->id,
                    'skill_id' => $skillId,
                    'skill_type' => 'methodological',
                    'is_required' => true,
                    'weight' => 4,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        // Compétences numériques
        if ($request->has('num_skills') && is_array($request->num_skills)) {
            foreach ($request->num_skills as $skillId) {
                $allSkills[] = [
                    'job_offer_id' => $offre->id,
                    'skill_id' => $skillId,
                    'skill_type' => 'digital',
                    'is_required' => true,
                    'weight' => 4,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        
        if (!empty($allSkills)) {
            JobOfferSkill::insert($allSkills);
        }

        // Recharger l'offre avec les relations
        $offre->load(['type', 'sector', 'diplomes']);

        return response()->json([
            'success' => true,
            'message' => 'Offre modifiée avec succès',
            'offre' => $offre
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Offre introuvable'
        ], 404);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Erreur lors de la modification de l\'offre', [
            'offre_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la modification de l\'offre: ' . $e->getMessage()
        ], 500);
    }
}




    public function destroy($id)
    {
        try {
            $offre = Offre::where('entreprise_id', Auth::user()->entreprise->id)
            ->where('id', $id)
            ->firstOrFail();
            
            $offre->postulations()->delete();
            
            $offre->delete();

            return response()->json([
                'success' => true,
                'message' => 'Offre et toutes ses postulations associées ont été supprimées avec succès'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Offre introuvable'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

 public function edit($id)
{
    try {
        $offre = Offre::with([
            'type',
            'sector',
            'diplomes' => function($query) {
                $query->withPivot('obligatoire');
            },
            'skills.skill'
        ])->findOrFail($id);
        
        // Vérifier que l'offre appartient à l'entreprise connectée
        if ($offre->entreprise_id !== Auth::user()->entreprise->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à modifier cette offre.'
            ], 403);
        }

        // Séparer les compétences par type
        $technicalSkills = $offre->skills->where('skill_type', 'technical')->pluck('skill');
        $methodologicalSkills = $offre->skills->where('skill_type', 'methodological')->pluck('skill');
        $digitalSkills = $offre->skills->where('skill_type', 'digital')->pluck('skill');

        // Préparer les données pour l'édition
        $offreData = $offre->toArray();
        $offreData['skillls'] = $technicalSkills;
        $offreData['methodological_skills'] = $methodologicalSkills;
        $offreData['language_skills'] = $digitalSkills;
        
        // Décoder les avantages si c'est du JSON
        if ($offre->avantages) {
            try {
                $benefits = json_decode($offre->avantages, true);
                if (is_array($benefits)) {
                    $offreData['benefits'] = $benefits;
                }
            } catch (\Exception $e) {
                $offreData['benefits'] = [];
            }
        }

        return response()->json([
            'success' => true,
            'offre' => $offreData
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Offre introuvable'
        ], 404);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Erreur lors du chargement de l\'offre pour édition', [
            'offre_id' => $id,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du chargement de l\'offre'
        ], 500);
    }
}

    public function search(Request $request)
    {
        $query = $request->get('search', '');
        
        $offres = Offre::where('entreprise_id', Auth::user()->entreprise->id)
                      ->where(function($q) use ($query) {
                          $q->where('titre', 'like', "%{$query}%")
                            ->orWhere('poste', 'like', "%{$query}%")
                            ->orWhere('localisation', 'like', "%{$query}%");
                      })
                      ->with(['type', 'categorie'])
                      ->paginate(9);

        return response()->json([
            'success' => true,
            'offres' => $offres
        ]);
    }


 public function searchia(Request $request)
{
    $query = $request->get('search', '');
    $entreprise = Auth::user()->entreprise;
    
    $offres = Offre::with(['type', 'categorie'])
        ->select('offres.*')
        ->join('postulations', 'offres.id', '=', 'postulations.offre_id')
        ->where('offres.entreprise_id', $entreprise->id)
        ->where('postulations.autopostulation', 1)
        ->where(function($q) use ($query) {
            $q->where('offres.titre', 'like', "%{$query}%")
              ->orWhere('offres.poste', 'like', "%{$query}%")
              ->orWhere('offres.localisation', 'like', "%{$query}%");
        })
        ->withCount([
            'postulations as total_postulations_count',
            'postulations as autopostulations_count' => function($q) {
                $q->where('autopostulation', 1);
            }
        ])
        ->distinct()
        ->orderBy('offres.created_at', 'desc')
        ->paginate(9);

    return response()->json([
        'success' => true,
        'offres' => $offres
    ]);
}

}
