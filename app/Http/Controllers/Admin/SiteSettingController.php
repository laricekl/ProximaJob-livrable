<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{   


    public function general()
    {
        $settings = SiteSetting::firstOrCreate([]);
        return view('admin.settings', ['settings' => $settings]);
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_nom' => 'required|string|max:255',
            'email' => 'required|email',
            'timezone' => 'required|timezone',
        ]);

        $settings = SiteSetting::first();
        $settings->update($validated);

        return back()->with('success', 'Paramètres généraux mis à jour');
    }

    public function updateLogo(Request $request)
    {
        $request->validate(['logo' => 'required|image|mimes:jpeg,png,svg|max:2048']);
        
        $settings = SiteSetting::first();
        
        // Supprimer l'ancien logo si existe
        if ($settings->logo) {
            Storage::delete($settings->logo);
        }
        
        $path = $request->file('logo')->store('public/settings');
        $settings->update(['logo' => str_replace('public/', '', $path)]);
        
        return response()->json([
            'logo_url' => $settings->logo_url
        ]);
    }

    public function removeLogo()
    {
        $settings = SiteSetting::first();
        
        if ($settings->logo) {
            Storage::delete('public/' . $settings->logo);
            $settings->update(['logo' => null]);
        }
        
        return response()->json(['success' => true]);
    }

    public function updateFavicon(Request $request)
    {
        $request->validate(['favicon' => 'required|image|mimes:ico,png|max:1024']);
        
        $settings = SiteSetting::first();
        
        // Supprimer l'ancien favicon si existe
        if ($settings->favicon) {
            Storage::delete($settings->favicon);
        }
        
        $path = $request->file('favicon')->store('public/settings');
        $settings->update(['favicon' => str_replace('public/', '', $path)]);
        
        return response()->json([
            'favicon_url' => $settings->favicon_url
        ]);
    }

    public function removeFavicon()
    {
        $settings = SiteSetting::first();
        
        if ($settings->favicon) {
            Storage::delete('public/' . $settings->favicon);
            $settings->update(['favicon' => null]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(SiteSetting $siteSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SiteSetting $siteSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SiteSetting $siteSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SiteSetting $siteSetting)
    {
        //
    }
}




 