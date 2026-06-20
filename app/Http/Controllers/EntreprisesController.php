<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Offre;
use App\Models\TypeOffre;
use App\Models\Categorie;
use App\Models\Postulation;
use App\Models\Diplome;
use App\Models\Skill; 
use App\Models\Sector;
use App\Models\User;
use App\Models\UserAbonnement;

class EntreprisesController extends Controller
{
    /**
     * Display a listing of the resource.
     */


         public function index(Request $request)
{
    // Récupérer l'entreprise de l'utilisateur connecté
    $entreprise = Auth()->user()->entreprise;
    $types = TypeOffre::get();
    $categories = Categorie::get();

    $diplomes = Diplome::get();
    
    // Récupérer les offres de cette entreprise avec pagination
    $offresQuery = $entreprise->offres()
                ->with(['categorie', 'type'])
                ->withCount('postulations')
                ->latest();

    if ($request->filled('search')) {
        $search = $request->search;
        $offresQuery->where(function ($query) use ($search) {
            $query->where('titre', 'like', "%{$search}%")
                ->orWhere('poste', 'like', "%{$search}%")
                ->orWhere('localisation', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $offres = $offresQuery->paginate(6)->withQueryString();

$notifications = \App\Models\Notification::where('user_id', Auth()->id())
                    ->where('role', 'entreprise')
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

    $unreadNotificationsCount = \App\Models\Notification::where('user_id', Auth()->id())
                                ->where('role', 'entreprise')
                                ->where('is_read', false)
                                ->count();


        $skillls = Skill::where('category', 'Hard Skills')->get(); 
        $methodologicalSkills = Skill::where('category', 'Soft Skills')->get(); 
        $numericSkills = Skill::where('category', 'Compétences numériques essentielles')->get();                        
        $sectors = Sector::get(); 

    return view("entreprise.offres-disponibles", compact(
        'offres', 
        'types',
        'categories',
        'diplomes',
        'notifications',
        'unreadNotificationsCount',
        'skillls',
        'sectors',
        'methodologicalSkills',
        'numericSkills'


    ));
}


    public function historique()
    {
        $entreprise = Auth::user()->entreprise;

        // Récupérer les offres paginées
        $diplomes = Diplome::get();

        
        // Récupérer les offres avec le compte des postulations
        $offres = Offre::with(['type', 'categorie'])
            ->withCount('postulations') // Ajoutez cette ligne pour compter les postulations
            ->where('entreprise_id', $entreprise->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculer les statistiques
        $stats = $this->calculateStats($entreprise->id);

        
        // Ajouter le nombre total de candidatures aux stats
        $stats['total_applications'] = $offres->sum('postulations_count');
        
        // Récupérer les types et catégories pour les formulaires
        $types = TypeOffre::all();
        $categories = Categorie::all();

        return view('entreprise.historique', compact(
            'offres',
            'stats',
            'types',
            'categories',
            'diplomes'
        ));

            
    }


    
    public function historique_ia()
    

{
    $entreprise = Auth::user()->entreprise;
    $diplomes = Diplome::get();

    // Approche alternative : utiliser join pour plus de performance
    $offres = Offre::with(['type', 'categorie'])
        ->select('offres.*')
        ->join('postulations', 'offres.id', '=', 'postulations.offre_id')
        ->where('offres.entreprise_id', $entreprise->id)
        ->where('postulations.autopostulation', 1)
        ->withCount([
            'postulations as total_postulations_count',
            'postulations as autopostulations_count' => function($query) {
                $query->where('autopostulation', 1);
            }
        ])
        ->distinct()  
        ->orderBy('offres.created_at', 'desc')
        ->paginate(10);

    
    $offres = Offre::with(['type', 'categorie'])
        ->whereExists(function($query) {
            $query->select('id')
                  ->from('postulations')
                  ->whereColumn('postulations.offre_id', 'offres.id')
                  ->where('postulations.autopostulation', 1);
        })
        ->withCount([
            'postulations',
            'postulations as autopostulations_count' => function($query) {
                $query->where('autopostulation', 1);
            }
        ])
        ->where('entreprise_id', $entreprise->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Calculer les statistiques
    $stats = $this->calculateStatsWithAutoPostulations($entreprise->id);
    
    $types = TypeOffre::all();
    $categories = Categorie::all();

    return view('entreprise.candidature-ia', compact(
        'offres', 
        'stats', 
        'types', 
        'categories',
        'diplomes'
    ));
}

// Méthode pour calculer rapidement les stats avec des requêtes optimisées
private function calculateStatsWithAutoPostulations($entrepriseId)
{
    // Une seule requête pour récupérer toutes les stats nécessaires
    $baseQuery = Postulation::whereHas('offre', function($query) use ($entrepriseId) {
        $query->where('entreprise_id', $entrepriseId);
    })->where('autopostulation', 1);

    $stats = [
        'total_applications' => $baseQuery->count(),
        'new_today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
        'new_this_week' => (clone $baseQuery)->where('created_at', '>=', now()->startOfWeek())->count(),
    ];

    // Offres actives avec postulations automatiques
    $stats['active_offers'] = Offre::where('entreprise_id', $entrepriseId)
        ->where('status', 'active')
        ->whereExists(function($query) {
            $query->select('id')
                  ->from('postulations')
                  ->whereColumn('postulations.offre_id', 'offres.id')
                  ->where('postulations.autopostulation', 1);
        })
        ->count();

    // Calcul du taux de clôture
    $totalOffers = Offre::where('entreprise_id', $entrepriseId)
        ->whereExists(function($query) {
            $query->select('id')
                  ->from('postulations')
                  ->whereColumn('postulations.offre_id', 'offres.id')
                  ->where('postulations.autopostulation', 1);
        })
        ->count();

    $closedOffers = Offre::where('entreprise_id', $entrepriseId)
        ->where('status', 'closed')
        ->whereExists(function($query) {
            $query->select('id')
                  ->from('postulations')
                  ->whereColumn('postulations.offre_id', 'offres.id')
                  ->where('postulations.autopostulation', 1);
        })
        ->count();

    $stats['closure_rate'] = $totalOffers > 0 ? round(($closedOffers / $totalOffers) * 100, 1) : 0;
    $stats['rate_change'] = 0;

    return $stats;
}
    
    public function abonnements()
    {
        $user = Auth::user();
        
        $userAbonnements = UserAbonnement::with(['abonnement'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(9);  
            
  
        return view("entreprise.abonne" , compact('userAbonnements'));
    }
     public function promotion()
    {
        $entreprise = Auth::user()->entreprise;

        $offers = Offre::with('type')
            ->withCount([
                'postulations',
                'postulations as autopostulations_count' => function ($query) {
                    $query->where('autopostulation', true);
                },
            ])
            ->where('entreprise_id', $entreprise->id)
            ->latest()
            ->paginate(10);

        $promotionStats = [
            'total_offers' => $offers->total(),
            'active_offers' => $offers->getCollection()->where('status', 'active')->count(),
            'autopostulations' => $offers->getCollection()->sum('autopostulations_count'),
        ];

        return view("entreprise.promotion-entreprise", compact('offers', 'promotionStats'));
    }
    public function candidat($id)
    {
        $candidat = User::with([
        'skills', 
        'candidateSector.sector',
        'candidateSector.diplome'
    ])->findOrFail($id);

        $postulation = Postulation::with(['offre.type', 'offre.entreprise'])
            ->where('user_id', $candidat->id)
            ->whereHas('offre', function ($query) {
                $query->where('entreprise_id', Auth::user()->entreprise->id);
            })
            ->latest()
            ->first();

        return view("entreprise.details-candidat", compact('candidat', 'postulation'));
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

        /**
     * Calculer les statistiques générales
     */
    private function calculateStats($entrepriseId)
    {
        $now = now();
        $thisWeek = $now->copy()->startOfWeek();
        $today = $now->copy()->startOfDay();
        $lastMonth = $now->copy()->subMonth();

        // Offres actives
        $activeOffers = Offre::where('entreprise_id', $entrepriseId)
            ->where('status', 'active')
            ->count();

        // Nouvelles offres cette semaine
        $newThisWeek = Offre::where('entreprise_id', $entrepriseId)
            ->where('created_at', '>=', $thisWeek)
            ->count();

        // Total des candidatures
        $totalApplications = Postulation::whereHas('offre', function ($query) use ($entrepriseId) {
            $query->where('entreprise_id', $entrepriseId);
        })->count();

        // Nouvelles candidatures aujourd'hui
        $newToday = Postulation::whereHas('offre', function ($query) use ($entrepriseId) {
            $query->where('entreprise_id', $entrepriseId);
        })->where('created_at', '>=', $today)->count();

        // Taux de clôture (offres fermées avec succès)
        $totalOffers = Offre::where('entreprise_id', $entrepriseId)->count();
        $closedOffers = Offre::where('entreprise_id', $entrepriseId)
            ->where('status', 'closed')
            ->count();

        $closureRate = $totalOffers > 0 ? round(($closedOffers / $totalOffers) * 100, 1) : 0;

        // Évolution du taux de clôture (par rapport au mois dernier)
        $lastMonthClosedOffers = Offre::where('entreprise_id', $entrepriseId)
            ->where('status', 'closed')
            ->where('updated_at', '<', $lastMonth)
            ->count();

        $lastMonthTotalOffers = Offre::where('entreprise_id', $entrepriseId)
            ->where('created_at', '<', $lastMonth)
            ->count();

        $lastMonthClosureRate = $lastMonthTotalOffers > 0 ?
            round(($lastMonthClosedOffers / $lastMonthTotalOffers) * 100, 1) : 0;

        $rateChange = $closureRate - $lastMonthClosureRate;

        return [
            'active_offers' => $activeOffers,
            'new_this_week' => $newThisWeek,
            'total_applications' => $totalApplications,
            'new_today' => $newToday,
            'closure_rate' => $closureRate,
            'rate_change' => round($rateChange, 1)
        ];
    }
}
