<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SectorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $secteurs = Sector::query()
            ->withCount(['offres', 'children'])
            ->when($search, fn($q) => $q->where('name', 'LIKE', '%'.$search.'%')->orWhere('scian_code', 'LIKE', '%'.$search.'%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $parents = Sector::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.secteurs.index', compact('secteurs', 'parents', 'search'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255|unique:sectors,name',
            'scian_code' => 'nullable|string|max:50',
            'parent_id'  => 'nullable|exists:sectors,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.secteurs.index')->withErrors($validator)->withInput();
        }

        Sector::create([
            'name'       => $request->name,
            'slug'       => Str::slug($request->name),
            'scian_code' => $request->scian_code,
            'parent_id'  => $request->parent_id,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.secteurs.index')->with('success', 'Secteur « '.$request->name.' » créé.');
    }

    public function update(Request $request, Sector $secteur)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255|unique:sectors,name,'.$secteur->id,
            'scian_code' => 'nullable|string|max:50',
            'parent_id'  => 'nullable|exists:sectors,id|not_in:'.$secteur->id,
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.secteurs.index')->withErrors($validator)->withInput();
        }

        $secteur->update([
            'name'       => $request->name,
            'slug'       => Str::slug($request->name),
            'scian_code' => $request->scian_code,
            'parent_id'  => $request->parent_id,
        ]);

        return redirect()->route('admin.secteurs.index')->with('success', 'Secteur mis à jour.');
    }

    public function destroy(Sector $secteur)
    {
        if ($secteur->offres()->count() > 0) {
            return redirect()->route('admin.secteurs.index')->with('error', 'Secteur lié à '.$secteur->offres()->count().' offre(s).');
        }
        if ($secteur->children()->count() > 0) {
            return redirect()->route('admin.secteurs.index')->with('error', 'Ce secteur a des sous-secteurs. Supprimez-les d\'abord.');
        }
        $name = $secteur->name;
        $secteur->delete();
        return redirect()->route('admin.secteurs.index')->with('success', 'Secteur « '.$name.' » supprimé.');
    }
}
