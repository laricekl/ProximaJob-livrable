<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // si tu veux associer auto un admin

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyage de cache des permissions Spatie (évite bugs en dev)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /* ----------------------------------
         | 1. Créer toutes les permissions
         |-----------------------------------*/
        $permissions = [
            // Offres
            'offres.view',
            'offres.create',
            'offres.edit',
            'offres.delete',

            // Postulations
            'postulations.apply',
            'postulations.view',
            'postulations.manage',

            // Abonnements
            'abonnements.view',
            'abonnements.subscribe',
            'abonnements.manage',

            // Ressources
            'ressources.view',
            'ressources.download',
            'ressources.manage',

            // Catégories & Types d'offres
            'categories.manage',
            'types_offres.manage',

            // Utilisateurs & Entreprises
            'users.view',
            'users.manage',
            'entreprises.approve',

            // MARKETING - Nouvelles permissions
            'marketing.dashboard.view',
            'marketing.analytics.view',
            'marketing.reports.view',
            'marketing.campaigns.manage',
            'marketing.content.create',
            'marketing.content.edit',
            'marketing.content.delete',
            'marketing.statistics.view',
            'marketing.export.data',
            'marketing.emails.manage',
            'marketing.seo.manage',
            'marketing.social.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web'],
                ['name' => $perm, 'guard_name' => 'web']
            );
        }

        /* ----------------------------------
         | 2. Créer les rôles
         |-----------------------------------*/
        $adminRole      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $entrepriseRole = Role::firstOrCreate(['name' => 'entreprise', 'guard_name' => 'web']);
        $candidatRole   = Role::firstOrCreate(['name' => 'candidat', 'guard_name' => 'web']);
        $marketingRole  = Role::firstOrCreate(['name' => 'Marketing', 'guard_name' => 'web']);  
        /* ----------------------------------
         | 3. Récupérer les permissions par nom
         |-----------------------------------*/
        $permModels = Permission::whereIn('name', $permissions)->get()->keyBy('name');

        // Aides rapides
        $p = fn ($name) => $permModels[$name];

        /* ----------------------------------
         | 4. Assigner permissions aux rôles
         |-----------------------------------*/

        // Admin = toutes les permissions
        $adminRole->syncPermissions($permModels->values());

        // Entreprise
        $entrepriseRole->syncPermissions([
            $p('offres.view'),
            $p('offres.create'),
            $p('offres.edit'),
            $p('offres.delete'),
            $p('postulations.manage'),
            $p('ressources.view'),
            $p('abonnements.subscribe'),
            $p('abonnements.view'),
        ]);

        // Candidat
        $candidatRole->syncPermissions([
            $p('offres.view'),
            $p('postulations.apply'),
            $p('postulations.view'),
            $p('ressources.view'),
            $p('abonnements.subscribe'),
            $p('abonnements.view'),
        ]);

        // Marketing
        $marketingRole->syncPermissions([
            // Permissions marketing spécifiques
            $p('marketing.dashboard.view'),
            $p('marketing.analytics.view'),
            $p('marketing.reports.view'),
            $p('marketing.campaigns.manage'),
            $p('marketing.content.create'),
            $p('marketing.content.edit'),
            $p('marketing.statistics.view'),
            $p('marketing.export.data'),
            $p('marketing.emails.manage'),
            $p('marketing.seo.manage'),
            $p('marketing.social.manage'),
            
            // Permissions générales (optionnel)
            $p('offres.view'), // Peut voir les offres pour analyser
            $p('users.view'), // Peut voir les statistiques utilisateurs
            $p('ressources.view'), // Peut voir les ressources
        ]);

        /* ----------------------------------
         | 5. (Optionnel) Créer des utilisateurs de test
         |-----------------------------------*/
        if (config('app.env') !== 'production') {
            // Super Admin
            $admin = User::where('email', 'admin@example.com')->first();
            if (! $admin) {
                $admin = User::create([
                    'name' => 'Super Admin',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('password'),
                ]);
            }
            if ($admin && ! $admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }

            // Marketing de test
            $marketingUser = User::where('email', 'marketing@example.com')->first();
            if (! $marketingUser) {
                $marketingUser = User::create([
                    'name' => 'Responsable Marketing',
                    'email' => 'marketing@example.com',
                    'password' => bcrypt('password'),
                ]);
            }
            if ($marketingUser && ! $marketingUser->hasRole('Marketing')) {
                $marketingUser->assignRole('Marketing');
            }
        }

        // Rafraîchir le cache Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->command->info(' Rôles et permissions créés avec succès !');
        $this->command->info(' Rôles disponibles: Admin, Entreprise, Candidat, Marketing');
    }
}