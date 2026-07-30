<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\AbonnementFonctionnalite;
use App\Models\Categorie;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\TypeOffre;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
    {
        // Récupération des villes distinctes
        $publicOffers = fn () => Offre::query()
            ->where('status', 'active')
            ->whereHas('entreprise.user', fn ($query) => $query->where('email', 'not like', '%.test'));
        $villes = $publicOffers()->select('localisation')->distinct()->pluck('localisation');

        // Récupération des catégories avec compteur
        $categories = Categorie::withCount('offres')->limit(6)->get();

        // Recherche et filtres
        $search = $request->get('search');

        $query = $publicOffers()->with(['entreprise.user', 'type', 'categorie'])
                    ->when($request->filled('localisation'), function($q) use ($request) {
                        $q->where('localisation', $request->localisation);
                    })
                    ->when($request->filled('categorie_id'), function($q) use ($request) {
                        $q->where('categorie_id', $request->categorie_id);
                    });

        // Filtrage par mots-clés
        if ($search) {
            $keywords = explode(' ', $search);
            $query->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('poste', 'LIKE', '%'.$keyword.'%')
                    ->orWhereHas('entreprise', function($q) use ($keyword) {
                        $q->where('company_name', 'LIKE', '%'.$keyword.'%');
                    });
                }
            });
        }

        // Récupérer les offres filtrées (limitées à 8 pour l'affichage d'accueil)
        $offres = $query->latest()->take(8)->get();

        // value actuel du filtre
        $localisations = $publicOffers()->select('localisation')
        ->distinct()
        ->whereNotNull('localisation')
        ->where('localisation', '!=', '')
        ->orderBy('localisation')
        ->pluck('localisation');

        $categoriesWithCount = Categorie::withCount('offres')
            ->orderBy('nom')
            ->get();
        //
        $totalOffres = $publicOffers()->count();
        $totalUtilisateurs = User::where('email', 'not like', '%.test')->count();
        $totalEntreprises = Entreprise::whereHas('user', fn ($query) => $query->where('email', 'not like', '%.test'))->count();

        $featuredEntreprises = Entreprise::where('is_featured', true)
            ->whereNotNull('verified_at')
            ->whereHas('user', fn ($query) => $query->where('email', 'not like', '%.test'))
            ->take(6)
            ->get();

         $abonnements = Abonnement::get();
         $fonctionnalites = AbonnementFonctionnalite::get();



        return view('welcome', compact('offres', 'villes', 'categories', 'search', 'localisations','categoriesWithCount', 'totalOffres', 'totalUtilisateurs','totalEntreprises', 'abonnements',  'fonctionnalites', 'featuredEntreprises'));
    }

    /**
     * Page publique d'une entreprise.
     */
    public function entreprise($id)
    {
        $entreprise = Entreprise::query()
            ->whereKey($id)
            ->where('is_featured', true)
            ->whereNotNull('verified_at')
            ->whereHas('user', fn ($query) => $query->where('email', 'not like', '%.test'))
            ->firstOrFail();
        $entreprise->load('user');
        $offres = $entreprise->offres()
            ->where('status', 'active')
            ->with(['type', 'categorie'])
            ->latest()
            ->paginate(6);
        $totalPublishedOffers = $entreprise->offres()
            ->where('status', '!=', 'draft')
            ->count();

        return view('parametres.entreprise-profil', compact('entreprise', 'offres', 'totalPublishedOffers'));
    }

    /**
     * Liste des entreprises autorisées à apparaître sur le site public.
     */
    public function entreprises()
    {
        $entreprises = Entreprise::query()
            ->where('is_featured', true)
            ->whereNotNull('verified_at')
            ->whereHas('user', fn ($query) => $query->where('email', 'not like', '%.test'))
            ->withCount([
                'offres as active_offers_count' => fn ($query) => $query->where('status', 'active'),
            ])
            ->orderBy('company_name')
            ->paginate(12);

        return view('parametres.entreprises', compact('entreprises'));
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

}
