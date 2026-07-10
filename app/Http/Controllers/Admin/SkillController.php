<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $skills = Skill::query()
            ->withCount('sectors')
            ->when($search, fn($q) => $q->where('name', 'LIKE', '%'.$search.'%')->orWhere('category', 'LIKE', '%'.$search.'%'))
            ->orderBy('category')->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.skills.index', compact('skills', 'search'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255|unique:skills,name',
            'category'         => 'required|string|in:technique,transversale,numerique,linguistique,gestion,commercial',
            'description'      => 'nullable|string|max:500',
            'importance_level' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.skills.index')->withErrors($validator)->withInput();
        }

        Skill::create([
            'name'             => $request->name,
            'slug'             => Str::slug($request->name),
            'category'         => $request->category,
            'description'      => $request->description,
            'importance_level' => $request->importance_level,
            'is_active'        => true,
        ]);

        return redirect()->route('admin.skills.index')->with('success', 'Compétence « '.$request->name.' » créée.');
    }

    public function update(Request $request, Skill $skill)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255|unique:skills,name,'.$skill->id,
            'category'         => 'required|string|in:technique,transversale,numerique,linguistique,gestion,commercial',
            'description'      => 'nullable|string|max:500',
            'importance_level' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.skills.index')->withErrors($validator)->withInput();
        }

        $skill->update([
            'name'             => $request->name,
            'slug'             => Str::slug($request->name),
            'category'         => $request->category,
            'description'      => $request->description,
            'importance_level' => $request->importance_level,
        ]);

        return redirect()->route('admin.skills.index')->with('success', 'Compétence mise à jour.');
    }

    public function destroy(Skill $skill)
    {
        $name = $skill->name;
        $skill->delete();
        return redirect()->route('admin.skills.index')->with('success', 'Compétence « '.$name.' » supprimée.');
    }
}
