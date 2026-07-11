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
        $villes = Offre::select('localisation')->distinct()->pluck('localisation');

        // Récupération des catégories avec compteur
        $categories = Categorie::withCount('offres')->limit(6)->get();

        // Recherche et filtres
        $search = $request->get('search');

        $query = Offre::with(['entreprise.user', 'type', 'categorie'])
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
        $localisations = Offre::select('localisation')
        ->distinct()
        ->whereNotNull('localisation')
        ->where('localisation', '!=', '')
        ->orderBy('localisation')
        ->pluck('localisation');

        $categoriesWithCount = Categorie::withCount('offres')
            ->orderBy('nom')
            ->get();
        //
        $totalOffres = Offre::count();
        $totalUtilisateurs = User::count();
        $totalEntreprises = Entreprise::count();

        $featuredEntreprises = Entreprise::where('is_featured', true)
            ->whereNotNull('verified_at')
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
        $entreprise = Entreprise::findOrFail($id);
        $entreprise->load('user');
        $offres = $entreprise->offres()
            ->where('status', 'active')
            ->with(['type', 'categorie'])
            ->latest()
            ->paginate(6);

        return view('parametres.entreprise-profil', compact('entreprise', 'offres'));
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
