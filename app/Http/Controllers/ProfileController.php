<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        if ($request->user()->entreprise) {
            return view('entreprise.profile', [
                'user' => $request->user(),
                'entreprise' => $request->user()->entreprise,
            ]);
        }

        return view('profile.edit', [
            'user' => $request->user()->load('cvProfile'),
            'cvProfile' => $request->user()->cvProfile,
        ]);
    }

    public function updateEnterprise(Request $request): RedirectResponse
    {
        $user = $request->user();
        $entreprise = $user->entreprise;
        abort_unless($entreprise, 404);

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'website' => ['nullable', 'url', 'max:255'],
            'neq' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(\App\Models\User::class)->ignore($user->id),
            ],
            'telephone' => ['nullable', 'string', 'max:30'],
            'adresse' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($entreprise->logo && Storage::disk('public')->exists($entreprise->logo)) {
                Storage::disk('public')->delete($entreprise->logo);
            }
            $validated['logo'] = $request->file('logo')->store('entreprises/logos', 'public');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
        ]);

        $entreprise->update([
            'company_name' => $validated['company_name'],
            'description' => $validated['description'] ?? null,
            'website' => $validated['website'] ?? null,
            'neq' => $validated['neq'] ?? null,
            'logo' => $validated['logo'] ?? $entreprise->logo,
        ]);

        return Redirect::route('profile.edit')->with('status', 'enterprise-profile-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();
        if ($request->user()->cvProfile) {
            $request->user()->cvProfile->update([
                'nom' => $request->user()->name,
                'email' => $request->user()->email,
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function changePassword(Request $request)
{
    // Validation des données
    $validator = Validator::make($request->all(), [
        'current_password' => ['required', 'string'],
        'new_password' => ['required', 'string', 'min:8', 'confirmed' ],
    ], [
        'current_password.required' => 'Le mot de passe actuel est requis',
        'new_password.required' => 'Le nouveau mot de passe est requis',
        'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères',
        'new_password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Récupérer l'utilisateur connecté
    $user = Auth::user();

    // Vérifier le mot de passe actuel
    if (!Hash::check($request->current_password, $user->password)) {
        return redirect()->back()
            ->with('error', 'Le mot de passe actuel est incorrect.')
            ->withInput();
    }

    // Vérifier que le nouveau mot de passe est différent de l'actuel
    if (Hash::check($request->new_password, $user->password)) {
        return redirect()->back()
            ->with('error', 'Le nouveau mot de passe doit être différent du mot de passe actuel.')
            ->withInput();
    }


    Auth::logoutOtherDevices($request->current_password);

    // Mettre à jour le mot de passe
    $user->update([
        'password' => Hash::make($request->new_password)
    ]);


    return redirect()->back()
        ->with('success', 'Votre mot de passe a été modifié avec succès.');
}
}
