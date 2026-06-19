<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Offre;
use App\Models\UserAbonnement;
use App\Models\Abonnement; 
use App\Models\AbonnementFonctionnalite;
use App\Models\Entreprise;
use App\Models\Notification;
use App\Models\Postulation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EntrepriseValidated;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $users = User::with(['roles', 'entreprise'])
                    ->latest()
                    ->take(5)
                    ->get();

         $entrepriseStats = [
            'count' => User::role('entreprise')->count(),
            'monthlyGrowth' => $this->calculateMonthlyGrowth('entreprise')
        ];

        $offreStats = [
            'count' => Offre::count(),
            'monthlyGrowth' => $this->calculateOffreMonthlyGrowth(Offre::class)
        ];

        $candidateStats = [
            'count' => User::role('candidat')->count(),
            'monthlyGrowth' => $this->calculateMonthlyGrowth('candidat')
        ];

        $matchStats = [
            'count' => Postulation::count(),
            'monthlyGrowth' => $this->calculateOffreMonthlyGrowth(Postulation::class)
        ];

         $userChartData = $this->getMonthlyData(User::class);
         $offerChartData = $this->getMonthlyData(Offre::class);


         return view("admin.index", compact('users', 'entrepriseStats', 'offreStats', 'candidateStats', 'matchStats', 'userChartData', 'offerChartData'));
    }

    private function calculateMonthlyGrowth($role): float
    {
        $currentMonthCount = User::role($role)
                            ->whereMonth('created_at', now()->month)
                            ->count();

        $previousMonthCount = User::role($role)
                            ->whereMonth('created_at', now()->subMonth()->month)
                            ->count();

        if ($previousMonthCount === 0) {
            return 0;
        }

        return round(($currentMonthCount - $previousMonthCount) / $previousMonthCount * 100, 1);
    }

    private function calculateOffreMonthlyGrowth($model): float
    {
        $currentMonthCount = $model::whereMonth('created_at', now()->month)->count();
        $previousMonthCount = $model::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($previousMonthCount === 0) {
            return 0;
        }

        return round(($currentMonthCount - $previousMonthCount) / $previousMonthCount * 100, 1);
    }

    private function getMonthlyData($model)
    {
        $currentYear = now()->year;
        $data = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $count = $model::whereYear('created_at', $currentYear)
                        ->whereMonth('created_at', $i)
                        ->count();
            $data[] = $count;
        }
        
        return [
            'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            'data' => $data,
            'currentYear' => $currentYear
        ];
    }

public function getChartData(Request $request)
{
    $type = $request->query('type');
    $period = $request->query('period', '12');
    
    // Validation des paramètres
    if (!in_array($type, ['users', 'offers'])) {
        return response()->json(['error' => 'Type invalide'], 400);
    }
    
    if (!in_array($period, ['12', '3', '1', '7'])) {
        return response()->json(['error' => 'Période invalide'], 400);
    }
    
    $model = $type === 'users' ? User::class : Offre::class;
    
    $endDate = now();
    $startDate = clone $endDate;
    
    $data = [];
    $labels = [];
    
    switch ($period) {
        case '3': // 3 derniers mois
            $startDate->subMonths(3);
            $data = $this->getMonthlyDataForPeriod($model, $startDate, $endDate);
            $labels = $this->getMonthLabels($startDate, $endDate);
            break;
            
        case '1': // 1 semaine (7 derniers jours)
            $startDate->subDays(7);
            $data = $this->getDailyDataForPeriod($model, $startDate, $endDate);
            $labels = $this->getDayLabels($startDate, $endDate);
            break;
            
        case '7': // 24 heures (par heure)
            $startDate->subDay();
            $data = $this->getHourlyDataForPeriod($model, $startDate, $endDate);
            $labels = $this->getHourLabels($startDate, $endDate);
            break;
            
        default: // 12 derniers mois
            $startDate->subMonths(12);
            $data = $this->getMonthlyDataForPeriod($model, $startDate, $endDate);
            $labels = $this->getMonthLabels($startDate, $endDate);
    }
    
    return response()->json([
        'labels' => $labels,
        'data' => $data,
        'period' => $period,
        'type' => $type
    ]);
}

private function getMonthlyDataForPeriod($model, $startDate, $endDate)
{
    $data = $model::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as date, COUNT(*) as count")
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date');
    
    $result = [];
    $current = clone $startDate;
    
    while ($current <= $endDate) {
        $key = $current->format('Y-m');
        $result[] = $data->get($key, 0);
        $current->addMonth();
    }
    
    return $result;
}

private function getDailyDataForPeriod($model, $startDate, $endDate)
{
    $data = $model::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw("DATE(created_at) as date, COUNT(*) as count")
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date');
    
    $result = [];
    $current = clone $startDate;
    
    while ($current <= $endDate) {
        $key = $current->format('Y-m-d');
        $result[] = $data->get($key, 0);
        $current->addDay();
    }
    
    return $result;
}

private function getHourlyDataForPeriod($model, $startDate, $endDate)
{
    $data = $model::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as date, COUNT(*) as count")
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date');
    
    $result = [];
    $current = clone $startDate;
    
    while ($current <= $endDate) {
        $key = $current->format('Y-m-d H:00:00');
        $result[] = $data->get($key, 0);
        $current->addHour();
    }
    
    return $result;
}

private function getMonthLabels($startDate, $endDate)
{
    $labels = [];
    $current = clone $startDate;
    
    while ($current <= $endDate) {
        $labels[] = $current->isoFormat('MMM YYYY');
        $current->addMonth();
    }
    
    return $labels;
}

private function getDayLabels($startDate, $endDate)
{
    $labels = [];
    $current = clone $startDate;
    
    while ($current <= $endDate) {
        $labels[] = $current->isoFormat('DD MMM');
        $current->addDay();
    }
    
    return $labels;
}

private function getHourLabels($startDate, $endDate)
{
    $labels = [];
    $current = clone $startDate;
    
    while ($current <= $endDate) {
        $labels[] = $current->format('H:i');
        $current->addHour();
    }
    
    return $labels;
}

 public function users(Request $request)
{
    $query = User::with(['roles', 'entreprise'])->orderBy('name');
    
    
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('prenom', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhereHas('entreprise', function ($entrepriseQuery) use ($search) {
                  $entrepriseQuery->where('company_name', 'like', "%$search%");
              });
        });
    }
    
    // Filtre par rôle
    if ($request->has('role') && !empty($request->role)) {
        $query->whereHas('roles', function($q) use ($request) {
            $q->where('name', $request->role);
        });
    }
    
    // Filtre par statut
    if ($request->has('status') && !empty($request->status)) {
        $status = $request->status;

        $query->where(function ($statusQuery) use ($status) {
            $statusQuery->where('status', $status)
                ->orWhereHas('entreprise', function ($entrepriseQuery) use ($status) {
                    $entrepriseQuery->where('status', $status);
                });
        });
    }
    
    $users = $query->paginate(10)->withQueryString();
    
    // Pour conserver les paramètres de filtre dans la pagination
    if ($request->ajax()) {
        return response()->json(['users' => $users]);
    }
    
    return view("admin.users", compact('users'));
}

    public function offres(Request $request)
    {
        $query = Offre::with(['entreprise', 'categorie', 'type']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($offerQuery) use ($search) {
                $offerQuery->where('titre', 'like', "%$search%")
                    ->orWhere('poste', 'like', "%$search%")
                    ->orWhere('localisation', 'like', "%$search%")
                    ->orWhereHas('entreprise', function ($entrepriseQuery) use ($search) {
                        $entrepriseQuery->where('company_name', 'like', "%$search%");
                    })
                    ->orWhereHas('categorie', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('nom', 'like', "%$search%");
                    });
            });
        }

        if ($request->filled('entreprise_id') && $request->entreprise_id != '') {
            $query->where('entreprise_id', $request->entreprise_id);
        }
        if ($request->filled('status') && $request->status != '') {
            if ($request->status === 'expire') {
                $query->whereDate('date_fin', '<', now())
                    ->where('status', '!=', 'desactive');
            } else {
                $query->where('status', $request->status);
            }
        } 
        $query->orderBy('created_at', 'desc');
        $offers = $query->paginate(10)->appends($request->query());
        $entreprises = Entreprise::get();
        
        return view("admin.offres", compact('offers', 'entreprises'));
    }

    public function deactivateOffer($id)
{
    try {
        $offer = Offre::findOrFail($id);
        
        // Mettre à jour le statut
        $offer->status = 'desactive';
        $offer->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Offre désactivée avec succès'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la désactivation de l\'offre'
        ], 500);
    }
}

 
/**
 * Réactiver une offre
 */
public function reactivateOffer($id)
{
    try {
        $offer = Offre::findOrFail($id);
        
        // Déterminer le nouveau statut
        $now = \Carbon\Carbon::now();
        $expirationDate = \Carbon\Carbon::parse($offer->date_fin);
        
        if ($now->gt($expirationDate)) {
            $newStatus = 'expire';
        } else {
            $newStatus = 'active';
        }
        
        // Mettre à jour le statut
        $offer->status = $newStatus;
        $offer->save();
        
        $message = $newStatus === 'expire' 
            ? 'Offre réactivée avec succès (mais expirée car la date limite est dépassée)'
            : 'Offre réactivée avec succès';
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $newStatus
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la réactivation de l\'offre'
        ], 500);
    }
}

public function abonnements(Request $request)
{
   
    $activeSubscriptions = UserAbonnement::where('date_fin', '>=', now())->count();
    $expiredSubscriptions = UserAbonnement::where('date_fin', '<', now())->count();
    
    // Calcul du revenu mensuel (version optimisée avec la nouvelle structure)
    $monthlyRevenue = UserAbonnement::whereBetween('date_debut', [now()->startOfMonth(), now()->endOfMonth()])
        ->join('abonnements', 'user_abonnements.abonnement_id', '=', 'abonnements.id')
        ->sum('abonnements.montant');

    // Abonnements à renouveler (expirent dans 7 jours)
    $toRenew = UserAbonnement::whereBetween('date_fin', [now(), now()->addDays(7)])->count();

    // Calcul de l'évolution des abonnements actifs
    $lastMonthActive = UserAbonnement::whereBetween('date_fin', [
        now()->subMonth()->startOfMonth(),
        now()->subMonth()->endOfMonth()
    ])->count();
    
    $activeChange = $lastMonthActive > 0 
        ? (($activeSubscriptions - $lastMonthActive) / $lastMonthActive * 100) 
        : 0;


    $query = UserAbonnement::with(['user.entreprise', 'abonnement'])
        ->orderBy('date_fin', 'desc');

    // Filtre par statut
    if ($request->has('status')) {
        switch ($request->status) {
            case 'active':
                $query->where('date_fin', '>=', now());
                break;
            case 'expired':
                $query->where('date_fin', '<', now());
                break;
            case 'pending':
                $query->where('status', 'En attente');
                break;
        }
    }
    
    // Filtre par plan
    if ($request->filled('plan')) {
        $query->where('abonnement_id', $request->plan);
    }

    // Pagination
    $abonnements = $query->paginate(10);
    
    // Liste des plans pour le filtre
    $plans = Abonnement::pluck('nom', 'id');
    $abonnementSummaries = Abonnement::withCount('users')
        ->orderByDesc('users_count')
        ->get();

    // Debug (optionnel)
    logger('Stats abonnements', [
        'total_actifs' => $activeSubscriptions,
        'total_expires' => $expiredSubscriptions,
        'revenue_mensuel' => $monthlyRevenue
    ]);

    return view("admin.abonnements", compact(
        'abonnements',
        'activeSubscriptions',
        'expiredSubscriptions',
        'monthlyRevenue',
        'toRenew',
        'activeChange',
        'plans',
        'abonnementSummaries'
    ));
}

    public function statistiques()
    {
         $users = User::with('roles')
                    ->latest()
                    ->take(3)
                    ->get();

         $entrepriseStats = [
            'count' => User::role('entreprise')->count(),
            'monthlyGrowth' => $this->calculateMonthlyGrowth('entreprise')
        ];

        $offreStats = [
            'count' => Offre::count(),
            'monthlyGrowth' => $this->calculateOffreMonthlyGrowth(Offre::class)
        ];

         $userChartData = $this->getMonthlyData(User::class);
         $offerChartData = $this->getMonthlyData(Offre::class);

         $totalUsers = max(User::count(), 1);
         $roleDistribution = [
            ['label' => 'Candidats', 'count' => User::role('candidat')->count(), 'color' => 'bg-secondary-container'],
            ['label' => 'Entreprises', 'count' => User::role('entreprise')->count(), 'color' => 'bg-blue-500'],
            ['label' => 'Admins', 'count' => User::role('admin')->count(), 'color' => 'bg-primary-container'],
         ];

         $totalPostulations = max(Postulation::count(), 1);
         $applicationStatusDistribution = collect([
            ['label' => 'Matchées', 'count' => Postulation::where('status', 'matchée')->count(), 'color' => 'bg-secondary-container'],
            ['label' => 'En cours', 'count' => Postulation::whereIn('status', ['en attente', 'en_cours'])->count(), 'color' => 'bg-amber-400'],
            ['label' => 'Entretien', 'count' => Postulation::where('status', 'entretien')->count(), 'color' => 'bg-blue-500'],
            ['label' => 'Refusées', 'count' => Postulation::whereIn('status', ['refusée', 'refusee'])->count(), 'color' => 'bg-red-400'],
         ])->filter(fn ($item) => $item['count'] > 0)->values();

         if ($applicationStatusDistribution->isEmpty()) {
            $applicationStatusDistribution = collect([
                ['label' => 'En cours', 'count' => Postulation::count(), 'color' => 'bg-secondary-container'],
            ]);
         }

         $topEntreprises = Entreprise::withCount('offres')
            ->with(['offres.postulations'])
            ->get()
            ->map(function (Entreprise $entreprise) {
                $candidaturesCount = $entreprise->offres->sum(fn ($offre) => $offre->postulations->count());
                $offersCount = max($entreprise->offres_count, 1);

                return [
                    'company_name' => $entreprise->company_name,
                    'offers_count' => $entreprise->offres_count,
                    'applications_count' => $candidaturesCount,
                    'matching_rate' => (int) round(min(100, ($candidaturesCount / $offersCount) * 10)),
                ];
            })
            ->sortByDesc('offers_count')
            ->take(5)
            ->values();

         return view("admin.statistiques", compact(
            'users',
            'entrepriseStats',
            'offreStats',
            'userChartData',
            'offerChartData',
            'roleDistribution',
            'totalUsers',
            'applicationStatusDistribution',
            'totalPostulations',
            'topEntreprises'
         ));
    }

    public function newsletters() 
    {
        
        return view("admin.newsletters" );
    }

  public function parametres()
    {



         $abonnements = Abonnement::with('fonctionnalites')->get();;
    
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

        return view("admin.parametres", compact('settings', 'timezones', 'abonnements'));
    }

    public function updateGeneral(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'site_nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'timezone' => 'required|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:1024',
        ], [
            'site_nom.required' => 'Le nom du site est obligatoire.',
            'email.required' => 'L\'email de contact est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être au format JPG, PNG ou SVG.',
            'logo.max' => 'Le logo ne doit pas dépasser 2MB.',
            'favicon.image' => 'Le favicon doit être une image.',
            'favicon.mimes' => 'Le favicon doit être au format ICO ou PNG.',
            'favicon.max' => 'Le favicon ne doit pas dépasser 1MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Récupérer ou créer les paramètres
            $settings = SiteSetting::first();
            if (!$settings) {
                $settings = new SiteSetting();
            }

            // Mise à jour des champs texte
            $settings->site_nom = $request->site_nom;
            $settings->email = $request->email;
            $settings->timezone = $request->timezone;

            // Gestion du logo
            if ($request->hasFile('logo')) {
                // Supprimer l'ancien logo s'il existe
                if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                    Storage::disk('public')->delete($settings->logo);
                }
                
                // Sauvegarder le nouveau logo
                $logoPath = $request->file('logo')->store('settings', 'public');
                $settings->logo = $logoPath;
            }

            // Gestion du favicon
            if ($request->hasFile('favicon')) {
                // Supprimer l'ancien favicon s'il existe
                if ($settings->favicon && Storage::disk('public')->exists($settings->favicon)) {
                    Storage::disk('public')->delete($settings->favicon);
                }
                
                // Sauvegarder le nouveau favicon
                $faviconPath = $request->file('favicon')->store('settings', 'public');
                $settings->favicon = $faviconPath;
            }

            $settings->save();

            return response()->json([
                'success' => true,
                'message' => 'Paramètres sauvegardés avec succès !',
                'data' => [
                    'logo_url' => $settings->logo_url,
                    'favicon_url' => $settings->favicon_url,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde : ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeLogo(Request $request)
    {
        try {
            $settings = SiteSetting::first();
            
            if ($settings && $settings->logo) {
                // Supprimer le fichier du storage
                if (Storage::disk('public')->exists($settings->logo)) {
                    Storage::disk('public')->delete($settings->logo);
                }
                
                // Mettre à jour la base de données
                $settings->logo = null;
                $settings->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logo supprimé avec succès !'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeFavicon(Request $request)
    {
        try {
            $settings = SiteSetting::first();
            
            if ($settings && $settings->favicon) {
                // Supprimer le fichier du storage
                if (Storage::disk('public')->exists($settings->favicon)) {
                    Storage::disk('public')->delete($settings->favicon);
                }
                
                // Mettre à jour la base de données
                $settings->favicon = null;
                $settings->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Favicon supprimé avec succès !'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }

    private function getUserInitials(User $user): string
    {
        return strtoupper(substr($user->name, 0, 1) . substr($user->prenom, 0, 1));
    }

    private function getUserColor(User $user): string
    {
        if ($user->hasRole('admin')) {
            return '#F59E0B'; // orange
        } elseif ($user->hasRole('entreprise')) {
            return '#3B82F6'; // bleu
        } else {
            return '#10B981'; // vert
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
        try {
            // Validation des données
            $rules = [
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,entreprise,candidat,Marketing',
                'telephone' => 'nullable|string|max:20',
                'adresse' => 'nullable|string|max:255',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];

            // Validation spécifique pour les entreprises
            if ($request->role === 'entreprise') {
                $rules['company_name'] = 'required|string|max:255';
                $rules['neq'] = 'required|string|max:50';
                $rules['rccm'] = 'nullable|string|max:50';
                $rules['website'] = 'nullable|url';
                $rules['description'] = 'nullable|string';
                $rules['logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
                $rules['extrait_rccm'] = 'nullable|mimes:pdf,doc,docx|max:10240';
            }

            $validatedData = $request->validate($rules);

            DB::beginTransaction();

            // Créer l'utilisateur
            $user = User::create([
                'name' => $validatedData['name'],
                'prenom' => $validatedData['prenom'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'telephone' => $validatedData['telephone'] ?? null,
                'adresse' => $validatedData['adresse'] ?? null,
                'status' => 'Actif'
            ]);

            // Assigner le rôle
           // $user->assignRole($validatedData['role']);
            $user->syncRoles([$validatedData['role']]);

            // Gérer la photo de profil
            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                $photoName = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('assets/images/user_pdp'), $photoName);
                $user->update(['profile_photo_path' => $photoName]);
            }

            // Créer les données entreprise  
            if ($validatedData['role'] === 'entreprise') {
                $entrepriseData = [
                    'user_id' => $user->id,
                    'company_name' => $validatedData['company_name'],
                    'neq' => $validatedData['neq'],
                    'rccm' => $validatedData['rccm'] ?? null,
                    'website' => $validatedData['website'] ?? null,
                    'description' => $validatedData['description'] ?? null,
                ];

                // Gérer le logo
                if ($request->hasFile('logo')) {
                    $logo = $request->file('logo');
                    $logoName = time() . '_' . $user->id . '_logo.' . $logo->getClientOriginalExtension();
                    $logo->move(public_path('assets/images/company_logos'), $logoName);
                    $entrepriseData['logo'] = $logoName;
                }

                // Gérer l'extrait RCCM
                if ($request->hasFile('extrait_rccm')) {
                    $extrait = $request->file('extrait_rccm');
                    $extraitName = time() . '_' . $user->id . '_extrait.' . $extrait->getClientOriginalExtension();
                    $extrait->move(public_path('assets/documents/rccm'), $extraitName);
                    $entrepriseData['extrait_rccm'] = $extraitName;
                }

                Entreprise::create($entrepriseData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès !'
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création : ' . $e->getMessage()
            ], 500);
        }
    }


       public function abonstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'duree' => 'required|integer',
            'couleur' => 'nullable|string',
            'populaire' => 'boolean',
            'actif' => 'boolean',
            'fonctionnalites' => 'required|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Créer l'abonnement
            $abonnement = Abonnement::create([
                'nom' => $request->nom,
                'description' => $request->description,
                'montant' => $request->montant,
                'duree' => $request->duree,
                'couleur' => $request->couleur,
                'populaire' => $request->boolean('populaire'),
                'actif' => $request->boolean('actif')
            ]);

            // Créer les fonctionnalités
            $fonctionnalites = json_decode($request->fonctionnalites, true);
            
            foreach ($fonctionnalites as $index => $fonctionnalite) {
                AbonnementFonctionnalite::create([
                    'abonnement_id' => $abonnement->id,
                    'nom' => $fonctionnalite['nom'],
                    'icone' => $fonctionnalite['icone'] ?? null,
                    'ordre' => $fonctionnalite['ordre'] ?? $index,
                    'actif' => $fonctionnalite['actif'] ?? true
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Abonnement créé avec succès',
                'data' => $abonnement
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFonctionnalites($id)
    {
        try {
            $abonnement = Abonnement::with('fonctionnalites')->findOrFail($id);
            return response()->json($abonnement->fonctionnalites);
        } catch (\Exception $e) {
            \Log::error('Erreur chargement fonctionnalités: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }


public function abonupdate(Request $request, $id)
{
    DB::beginTransaction();
    
    try {
        $abonnement = Abonnement::findOrFail($id);
        
        // Validation des données avec des règles plus flexibles
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'duree' => 'required|integer|min:0',
            'couleur' => 'nullable|string',
            'populaire' => 'sometimes|in:0,1',
            'actif' => 'sometimes|in:0,1',
            'fonctionnalites' => 'required|string' // JSON string, pas json validation
        ]);

        if ($validator->fails()) {
            \Log::info('Erreurs de validation:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Mise à jour de l'abonnement avec gestion des valeurs par défaut
        $abonnement->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'montant' => $request->montant,
            'duree' => $request->duree,
            'couleur' => $request->couleur,
            'populaire' => $request->populaire == '1' ? true : false,
            'actif' => $request->actif == '1' ? true : false
        ]);
        
        // Gestion des fonctionnalités
        $fonctionnalitesJson = $request->fonctionnalites;
        \Log::info('JSON fonctionnalités reçu:', ['json' => $fonctionnalitesJson]);
        
        $fonctionnalites = json_decode($fonctionnalitesJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Erreur de décodage JSON des fonctionnalités: ' . json_last_error_msg());
        }
        
        \Log::info('Fonctionnalités décodées:', ['fonctionnalites' => $fonctionnalites]);
        
        // Supprimer les anciennes fonctionnalités
        $abonnement->fonctionnalites()->delete();
        
        // Ajouter les nouvelles fonctionnalités
        foreach ($fonctionnalites as $index => $fnData) {
            if (!empty($fnData['nom'])) {
                $newFonctionnalite = AbonnementFonctionnalite::create([
                    'abonnement_id' => $abonnement->id,
                    'nom' => $fnData['nom'],
                    'icone' => $fnData['icone'] ?? null,
                    'ordre' => $fnData['ordre'] ?? $index,
                    'actif' => isset($fnData['actif']) ? (bool) $fnData['actif'] : true,
                ]);
                
                \Log::info('Fonctionnalité créée:', ['fonctionnalite' => $newFonctionnalite->toArray()]);
            }
        }
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Abonnement mis à jour avec succès',
            'data' => $abonnement->load('fonctionnalites')
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Erreur mise à jour abonnement: ' . $e->getMessage(), [
            'exception' => $e,
            'abonnement_id' => $id,
            'request_data' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
        ], 500);
    }
}

 public function abondestroy($id)
{
    DB::beginTransaction();
    
    try {
        $abonnement = Abonnement::with(['users', 'fonctionnalites'])->findOrFail($id);
        
                
        // Log avant suppression
        \Log::info('Suppression d\'abonnement demandée', [
            'abonnement_id' => $id,
            'nom' => $abonnement->nom,
            'fonctionnalites_count' => $abonnement->fonctionnalites->count(),
            'utilisateurs_historique' => $abonnement->users->count()
        ]);
        
        // Supprimer les fonctionnalités associées
        $fonctionnalitesCount = $abonnement->fonctionnalites->count();
        $abonnement->fonctionnalites()->delete();
        
        // Détacher tous les utilisateurs (supprimer les enregistrements pivot)
        $abonnement->users()->detach();
        
        // Supprimer l'abonnement
        $nomAbonnement = $abonnement->nom;
        $abonnement->delete();
        
        DB::commit();
        
        \Log::info('Abonnement supprimé avec succès', [
            'abonnement_id' => $id,
            'nom' => $nomAbonnement,
            'fonctionnalites_supprimees' => $fonctionnalitesCount
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "L'abonnement \"{$nomAbonnement}\" et ses {$fonctionnalitesCount} fonctionnalités ont été supprimés avec succès."
        ]);
        
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Abonnement non trouvé.'
        ], 404);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Erreur lors de la suppression d\'abonnement', [
            'abonnement_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Display the specified resource.
     */
public function show($id)
{
    $user = User::with([
                'entreprise',
                'roles',
                'postulations.offre.entreprise',
                'abonnements' => fn ($query) => $query->orderByDesc('user_abonnements.created_at'),
            ])
                ->findOrFail($id);

    return view('admin.users-show', compact('user'));
}


    
    /**
     * Show the form for editing the specified resource.
     */
    

/**
 * Récupère les données d'un utilisateur pour l'édition
 */
 public function edit($id)
    {
        try {
            $user = User::with(['roles', 'entreprise'])->findOrFail($id);
            
            // Formatter les données pour le frontend
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'adresse' => $user->adresse,
                'profile_photo_path' => $user->profile_photo_path,
                'roles' => $user->roles->pluck('name')->toArray(),
                'entreprise' => $user->entreprise
            ];
            
            return response()->json([
                'success' => true,
                'user' => $userData
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement des données utilisateur: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
    }

/**
 * Met à jour un utilisateur
 */
     public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Validation des données
            $rules = [
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'telephone' => 'nullable|string|max:20',
                'adresse' => 'nullable|string|max:255',
                'role' => 'required|in:admin,entreprise,candidat,Marketing',
                'password' => 'nullable|min:8|confirmed',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ];
            
            // Règles supplémentaires pour les entreprises
            if ($request->role === 'entreprise') {
                $rules = array_merge($rules, [
                    'company_name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'website' => 'nullable|url',
                    'neq' => 'nullable|string|max:50',
                    'rccm' => 'nullable|string|max:50',
                    'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'extrait_rccm' => 'nullable|file|mimes:pdf,doc,docx|max:5120'
                ]);
            }
            
            $validatedData = $request->validate($rules);
            
            // Commencer une transaction
            DB::beginTransaction();
            
            // Mettre à jour les informations utilisateur
            $userData = [
                'name' => $validatedData['name'],
                'prenom' => $validatedData['prenom'],
                'email' => $validatedData['email'],
                'telephone' => $validatedData['telephone'] ?? null,
                'adresse' => $validatedData['adresse'] ?? null,
            ];
            
            // Gérer le mot de passe s'il est fourni
            if (!empty($validatedData['password'])) {
                $userData['password'] = Hash::make($validatedData['password']);
            }
            
            // Gérer l'upload de la photo de profil
            if ($request->hasFile('profile_photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($user->profile_photo_path) {
                    $oldPhotoPath = public_path('assets/images/user_pdp/' . $user->profile_photo_path);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                
                // Uploader la nouvelle photo
                $photo = $request->file('profile_photo');
                $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('assets/images/user_pdp'), $photoName);
                $userData['profile_photo_path'] = $photoName;
            }
            
            // Mettre à jour l'utilisateur
            $user->update($userData);
            
            // Gérer les rôles
            $user->syncRoles([$validatedData['role']]);
            
            // Si le rôle est entreprise, gérer les données entreprise
            if ($validatedData['role'] === 'entreprise') {
                $entrepriseData = [
                    'company_name' => $validatedData['company_name'],
                    'description' => $validatedData['description'] ?? null,
                    'website' => $validatedData['website'] ?? null,
                    'neq' => $validatedData['neq'] ?? null,
                    'rccm' => $validatedData['rccm'] ?? null,
                ];
                
                $entreprise = $user->entreprise;
                
                // Gérer l'upload du logo
                if ($request->hasFile('logo')) {
                    // Supprimer l'ancien logo si il existe
                    if ($entreprise && $entreprise->logo) {
                        $oldLogoPath = public_path('assets/images/company_logos/' . $entreprise->logo);
                        if (file_exists($oldLogoPath)) {
                            unlink($oldLogoPath);
                        }
                    }
                    
                    $logo = $request->file('logo');
                    $logoName = time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
                    $logo->move(public_path('assets/images/company_logos'), $logoName);
                    $entrepriseData['logo'] = $logoName;
                }
                
                // Gérer l'upload de l'extrait RCCM
                if ($request->hasFile('extrait_rccm')) {
                    // Supprimer l'ancien fichier si il existe
                    if ($entreprise && $entreprise->extrait_rccm) {
                        $oldFilePath = public_path('assets/documents/rccm/' . $entreprise->extrait_rccm);
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    
                    $file = $request->file('extrait_rccm');
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('assets/documents/rccm'), $fileName);
                    $entrepriseData['extrait_rccm'] = $fileName;
                }
                
                // Créer ou mettre à jour l'entreprise
                if ($entreprise) {
                    $entreprise->update($entrepriseData);
                } else {
                    $entrepriseData['user_id'] = $user->id;
                    Entreprise::create($entrepriseData);
                }
            } else {
                // Si le rôle n'est plus entreprise, supprimer l'entreprise associée
                if ($user->entreprise) {
                    // Supprimer les fichiers associés
                    if ($user->entreprise->logo) {
                        $logoPath = public_path('assets/images/company_logos/' . $user->entreprise->logo);
                        if (file_exists($logoPath)) {
                            unlink($logoPath);
                        }
                    }
                    if ($user->entreprise->extrait_rccm) {
                        $filePath = public_path('assets/documents/rccm/' . $user->entreprise->extrait_rccm);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                    $user->entreprise->delete();
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur modifié avec succès'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la modification utilisateur: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
   public function deleteUser(Request $request, $id)
{
    try {
        // Validation des données
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        // Vérifier le mot de passe de l'administrateur connecté
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect. Vérifiez votre mot de passe et réessayez.'
            ], 422);
        }

        // Trouver l'utilisateur à supprimer
        $userToDelete = User::findOrFail($id);

        // Empêcher l'auto-suppression
        if ($userToDelete->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ], 422);
        }

        // Empêcher la suppression du super admin (optionnel)
        if ($userToDelete->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer un super administrateur.'
            ], 422);
        }

        // Sauvegarder les informations pour le message de retour
        $userName = $userToDelete->name . ' ' . $userToDelete->prenom;

        // Supprimer les fichiers associés
        $this->deleteUserFiles($userToDelete);

        // Supprimer l'utilisateur
        $userToDelete->delete();

        // Log de l'action (optionnel)
        \Log::info("Utilisateur supprimé", [
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'deleted_user_id' => $id,
            'deleted_user_name' => $userName,
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "L'utilisateur {$userName} a été supprimé avec succès."
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Données invalides.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Utilisateur non trouvé.'
        ], 404);
    } catch (\Exception $e) {
        \Log::error("Erreur lors de la suppression de l'utilisateur", [
            'error' => $e->getMessage(),
            'user_id' => $id,
            'admin_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la suppression.'
        ], 500);
    }
}

/**
 * Supprimer les fichiers associés à un utilisateur
 */
private function deleteUserFiles(User $user)
{
    // Supprimer la photo de profil
    if ($user->profile_photo_path && Storage::disk('public')->exists('assets/images/user_pdp/' . $user->profile_photo_path)) {
        Storage::disk('public')->delete('assets/images/user_pdp/' . $user->profile_photo_path);
    }

    // Si c'est une entreprise, supprimer les fichiers d'entreprise
    if ($user->hasRole('entreprise') && $user->entreprise) {
        $entreprise = $user->entreprise;
        
        // Supprimer le logo
        if ($entreprise->logo && Storage::disk('public')->exists('assets/images/company_logos/' . $entreprise->logo)) {
            Storage::disk('public')->delete('assets/images/company_logos/' . $entreprise->logo);
        }
        
        // Supprimer l'extrait RCCM
        if ($entreprise->extrait_rccm && Storage::disk('public')->exists('assets/documents/rccm/' . $entreprise->extrait_rccm)) {
            Storage::disk('public')->delete('assets/documents/rccm/' . $entreprise->extrait_rccm);
        }
    }
}

public function suspendUser(Request $request, $id)
{
    try {
        // Validation des données
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        // Vérifier le mot de passe de l'administrateur connecté
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect. Vérifiez votre mot de passe et réessayez.'
            ], 422);
        }

        // Trouver l'utilisateur à suspendre
        $userToSuspend = User::findOrFail($id);

        // Empêcher l'auto-suspension
        if ($userToSuspend->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas suspendre votre propre compte.'
            ], 422);
        }

        
      //  if ($userToSuspend->hasRole('super-admin')) {
        //    return response()->json([
          //      'success' => false,
          //      'message' => 'Impossible de suspendre un super administrateur.'
          //  ], 422);
       // }

        // Vérifier si l'utilisateur n'est pas déjà suspendu
        if ($userToSuspend->status === 'Suspendu') {
            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur est déjà suspendu.'
            ], 422);
        }

        // Sauvegarder les informations pour le message de retour
        $userName = $userToSuspend->name . ' ' . $userToSuspend->prenom;

        // Suspendre l'utilisateur
        $userToSuspend->update([
            'status' => 'Suspendu'
        ]);

        // Log de l'action (optionnel)
        \Log::info("Utilisateur suspendu", [
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'suspended_user_id' => $id,
            'suspended_user_name' => $userName,
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "L'utilisateur {$userName} a été suspendu avec succès."
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Données invalides.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Utilisateur non trouvé.'
        ], 404);
    } catch (\Exception $e) {
        \Log::error("Erreur lors de la suspension de l'utilisateur", [
            'error' => $e->getMessage(),
            'user_id' => $id,
            'admin_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la suspension.'
        ], 500);
    }
}

/**
 * Réactiver un utilisateur suspendu avec vérification du mot de passe
 */
public function reactivateUser(Request $request, $id)
{
    try {
        // Validation des données
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        // Vérifier le mot de passe de l'administrateur connecté
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect. Vérifiez votre mot de passe et réessayez.'
            ], 422);
        }

        // Trouver l'utilisateur à réactiver
        $userToReactivate = User::findOrFail($id);

        // Vérifier si l'utilisateur est bien suspendu
        if ($userToReactivate->status === 'Actif') {
            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur n\'est pas suspendu.'
            ], 422);
        }

        // Sauvegarder les informations pour le message de retour
        $userName = $userToReactivate->name . ' ' . $userToReactivate->prenom;

        // Réactiver l'utilisateur
        $userToReactivate->update([
            'status' => 'Actif'
        ]);

        return response()->json([
            'success' => true,
            'message' => "L'utilisateur {$userName} a été réactivé avec succès."
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Données invalides.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Utilisateur non trouvé.'
        ], 404);
    } catch (\Exception $e) {
        \Log::error("Erreur lors de la réactivation de l'utilisateur", [
            'error' => $e->getMessage(),
            'user_id' => $id,
            'admin_id' => Auth::id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la réactivation.'
        ], 500);
    }
}


  /*  public function validateEntreprise(Request $request)
    {
        try {
            // Validation des données d'entrée
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'password' => 'required|string'
            ]);

            // Vérifier le mot de passe admin
            if (!Hash::check($request->password, Auth::user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect'
                ], 422);
            }

            // Récupérer l'utilisateur avec son entreprise
            $user = User::with('entreprise')->find($request->user_id);

            // Vérifier que l'utilisateur a une entreprise
            if (!$user->entreprise) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune entreprise trouvée pour cet utilisateur'
                ], 404);
            }

            // Valider l'entreprise
            $user->entreprise->update([
                'status' => 'approved',
                'verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entreprise validée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }*/

         public function validateEntreprise(Request $request)
    {
        try {
            // Validation des données d'entrée
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'password' => 'required|string'
            ]);

            // Vérifier le mot de passe admin
            if (!Hash::check($request->password, Auth::user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect'
                ], 422);
            }

            // Récupérer l'utilisateur avec son entreprise
            $user = User::with('entreprise')->find($request->user_id);

            // Vérifier que l'utilisateur a une entreprise
            if (!$user->entreprise) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune entreprise trouvée pour cet utilisateur'
                ], 404);
            }

            // Vérifier que l'entreprise n'est pas déjà approuvée
            if ($user->entreprise->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette entreprise est déjà validée'
                ], 422);
            }

            // Valider l'entreprise
            $user->entreprise->update([
                'status' => 'approved',
                'verified_at' => now()
            ]);

            // Activer le compte utilisateur si nécessaire
            if ($user->status === 'Suspendu' || $user->status === 'inactive') {
                $user->update(['status' => 'Actif']);
            }

            // Envoyer l'email de confirmation
            try {
                Mail::to($user->email)->send(new EntrepriseValidated($user));
                
                Log::info('Email de validation entreprise envoyé', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'company_name' => $user->entreprise->company_name
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Entreprise validée avec succès et email de confirmation envoyé à ' . $user->email
                ]);

            } catch (\Exception $mailException) {
                // Logger l'erreur d'envoi d'email
                Log::error('Erreur lors de l\'envoi de l\'email de validation entreprise', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $mailException->getMessage()
                ]);

                // L'entreprise est validée mais l'email n'a pas pu être envoyé
                return response()->json([
                    'success' => true,
                    'message' => 'Entreprise validée avec succès, mais l\'email de confirmation n\'a pas pu être envoyé. Veuillez contacter l\'utilisateur manuellement.',
                    'warning' => 'Email non envoyé'
                ]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données de validation invalides',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation de l\'entreprise', [
                'user_id' => $request->user_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeter une entreprise (optionnel - si vous souhaitez implémenter cette fonctionnalité)
     */
    public function rejectEntreprise(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'password' => 'required|string',
                'reason' => 'nullable|string|max:500'
            ]);

            if (!Hash::check($request->password, Auth::user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect'
                ], 422);
            }

            $user = User::with('entreprise')->find($request->user_id);

            if (!$user->entreprise) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune entreprise trouvée pour cet utilisateur'
                ], 404);
            }

            $user->entreprise->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason
            ]);

            // Vous pouvez également envoyer un email de rejet ici si vous créez un autre Mailable

            Log::info('Entreprise rejetée', [
                'user_id' => $user->id,
                'company_name' => $user->entreprise->company_name,
                'reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entreprise rejetée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du rejet de l\'entreprise', [
                'user_id' => $request->user_id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

 public function activate(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'password' => 'required|string'
    ]);

    // Vérifier le mot de passe de l'admin
    if (!Hash::check($request->password, auth()->user()->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Mot de passe incorrect'
        ], 403);
    }

    // Récupérer l'utilisateur par son ID
    $user = User::findOrFail($request->user_id);

    // Activer l'utilisateur
    $user->update([
        'status' => 'Actif',
        'is_active' => true
    ]);

    // Si c'est une entreprise, activer aussi l'entreprise
   /* if ($user->entreprise) {
        $user->entreprise->update([
            'status' => 'approved',
            'verified_at' => now()
        ]);
    } */

    // Envoyer une notification à l'utilisateur
    Notification::create([
        'user_id' => $user->id,
        'role' => $user->getRoleNames()->first(),
        'title' => 'Votre compte a été activé',
        'message' => 'Votre compte sur ProximaJob a été activé par un administrateur. Vous pouvez maintenant vous connecter et utiliser toutes les fonctionnalités.',
        'link' => '/login',
        'is_read' => false,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Compte activé avec succès'
    ]);
}


}
