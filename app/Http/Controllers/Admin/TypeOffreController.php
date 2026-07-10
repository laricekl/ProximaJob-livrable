<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeOffre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeOffreController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $types = TypeOffre::query()
            ->withCount('offres')
            ->when($search, fn($q) => $q->where('nom', 'LIKE', '%'.$search.'%'))
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        return view('admin.types-offres.index', compact('types', 'search'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:types_offres,nom',
        ], ['nom.unique' => 'Ce type d\'offre existe déjà.']);

        if ($validator->fails()) {
            return redirect()->route('admin.types-offres.index')->withErrors($validator)->withInput();
        }

        TypeOffre::create(['nom' => $request->nom]);

        return redirect()->route('admin.types-offres.index')->with('success', 'Type « '.$request->nom.' » créé.');
    }

    public function update(Request $request, TypeOffre $typeOffre)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:types_offres,nom,'.$typeOffre->id,
        ], ['nom.unique' => 'Ce type d\'offre existe déjà.']);

        if ($validator->fails()) {
            return redirect()->route('admin.types-offres.index')->withErrors($validator)->withInput();
        }

        $old = $typeOffre->nom;
        $typeOffre->update(['nom' => $request->nom]);

        return redirect()->route('admin.types-offres.index')->with('success', '« '.$old.' » → « '.$request->nom.' ».');
    }

    public function destroy(TypeOffre $typeOffre)
    {
        if ($typeOffre->offres()->count() > 0) {
            return redirect()->route('admin.types-offres.index')->with('error', 'Type lié à '.$typeOffre->offres()->count().' offre(s).');
        }
        $nom = $typeOffre->nom;
        $typeOffre->delete();
        return redirect()->route('admin.types-offres.index')->with('success', 'Type « '.$nom.' » supprimé.');
    }
}
