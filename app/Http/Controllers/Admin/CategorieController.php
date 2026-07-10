<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategorieController extends Controller
{
    /**
     * Liste toutes les catégories.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $categories = Categorie::query()
            ->withCount('offres')
            ->when($search, function ($query) use ($search) {
                $query->where('nom', 'LIKE', '%' . $search . '%');
            })
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    /**
     * Crée une nouvelle catégorie.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:categories,nom',
        ], [
            'nom.unique' => 'Cette catégorie existe déjà.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.categories.index')
                ->withErrors($validator)
                ->withInput();
        }

        Categorie::create(['nom' => $request->nom]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie « ' . $request->nom . ' » créée avec succès.');
    }

    /**
     * Met à jour une catégorie existante.
     */
    public function update(Request $request, Categorie $categorie)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:categories,nom,' . $categorie->id,
        ], [
            'nom.unique' => 'Cette catégorie existe déjà.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.categories.index')
                ->withErrors($validator)
                ->withInput();
        }

        $oldNom = $categorie->nom;
        $categorie->update(['nom' => $request->nom]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie « ' . $oldNom . ' » renommée en « ' . $request->nom . ' ».');
    }

    /**
     * Supprime une catégorie.
     */
    public function destroy(Categorie $categorie)
    {
        $offresCount = $categorie->offres()->count();

        if ($offresCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer « ' . $categorie->nom . ' » : ' . $offresCount . ' offre(s) y sont associée(s).');
        }

        $nom = $categorie->nom;
        $categorie->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie « ' . $nom . ' » supprimée.');
    }
}
