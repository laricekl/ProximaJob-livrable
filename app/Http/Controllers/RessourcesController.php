<?php

namespace App\Http\Controllers;

use App\Models\Ressource;
use Illuminate\Http\Request;

class RessourcesController extends Controller
{
    public function index()
    {
        // Récupérer toutes les ressources
        $ressources = Ressource::orderBy('created_at', 'desc')->get();
        
        // Calculer les statistiques par type
        $stats = [
            'offre_emploi' => Ressource::where('type', 'offre_emploi')->count(),
            'document' => Ressource::where('type', 'document')->count(),
            'video' => Ressource::where('type', 'video')->count(),
            'lien' => Ressource::where('type', 'lien')->count(),
        ];
        
        return view('ressources.ressources', compact('ressources', 'stats'));
    }
    
    // Méthode AJAX pour filtrer les ressources
    public function filter(Request $request)
    {
        $query = Ressource::query();
        
        // Filtre par type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Filtre par recherche
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('titre', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }
        
        $ressources = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'resources' => $ressources,
            'count' => $ressources->count()
        ]);
    }
}