<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\TypeOffre;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création des catégories
        $categories = [
            ['nom' => 'Informatique & Technologie', 'couleur' => '#3b82f6', 'icone' => 'fas fa-laptop-code'],
            ['nom' => 'Santé & Social', 'couleur' => '#ef4444', 'icone' => 'fas fa-user-md'],
            ['nom' => 'Éducation', 'couleur' => '#10b981', 'icone' => 'fas fa-graduation-cap'],
            ['nom' => 'Vente & Marketing', 'couleur' => '#f59e0b', 'icone' => 'fas fa-shopping-cart'],
            ['nom' => 'Finance', 'couleur' => '#6366f1', 'icone' => 'fas fa-money-bill-wave'],
        ];

        foreach ($categories as $cat) {
            Categorie::updateOrCreate(['nom' => $cat['nom']], $cat);
        }

        // 2. Création des types d'offres
        $types = [
            ['id' => 1, 'nom' => 'Temps plein'],
            ['id' => 2, 'nom' => 'Temps partiel'],
            ['id' => 3, 'nom' => 'Contractuel'],
            ['id' => 4, 'nom' => 'Stage'],
        ];

        foreach ($types as $type) {
            TypeOffre::updateOrCreate(['id' => $type['id']], $type);
        }

        // 3. Création des entreprises (et utilisateurs associés)
        $entreprisesData = [
            [
                'name' => 'Tech Solutions Inc.',
                'email' => 'contact@techsolutions.com',
                'description' => 'Leader en innovation technologique.',
                'website' => 'https://techsolutions.com',
            ],
            [
                'name' => 'Global Health Group',
                'email' => 'info@globalhealth.com',
                'description' => 'Fournisseur de soins de santé de classe mondiale.',
                'website' => 'https://globalhealth.com',
            ],
            [
                'name' => 'Future Education Academy',
                'email' => 'admin@futureacademy.ca',
                'description' => 'L\'excellence en éducation.',
                'website' => 'https://futureacademy.ca',
            ],
        ];

        foreach ($entreprisesData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                ]
            );

            // S'assurer qu'il a le rôle entreprise (Spatie Permission)
            if (!$user->hasRole('entreprise')) {
                $user->assignRole('entreprise');
            }

            $entreprise = Entreprise::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $data['name'],
                    'description' => $data['description'],
                    'website' => $data['website'],
                    'status' => 'verified',
                ]
            );

            // 4. Création de quelques offres par entreprise
            $jobs = [
                [
                    'titre' => 'Développeur Fullstack PHP/Laravel',
                    'poste' => 'Développeur Senior',
                    'description' => 'Nous recherchons un développeur passionné pour rejoindre notre équipe.',
                    'localisation' => 'Montréal, QC',
                    'experience' => '3-5 ans',
                    'salaire_min' => 75000,
                    'salaire_max' => 110000,
                    'type_id' => 1,
                    'categorie_id' => 1,
                ],
                [
                    'titre' => 'Infirmier / Infirmière Clinicien(ne)',
                    'poste' => 'Infirmier spécialisé',
                    'description' => 'Poste permanent dans notre département de soins intensifs.',
                    'localisation' => 'Québec, QC',
                    'experience' => '1-2 ans',
                    'salaire_min' => 60000,
                    'salaire_max' => 85000,
                    'type_id' => 1,
                    'categorie_id' => 2,
                ],
                [
                    'titre' => 'Enseignant en Informatique',
                    'poste' => 'Professeur',
                    'description' => 'Enseignement pour les niveaux collégiaux.',
                    'localisation' => 'Sherbrooke, QC',
                    'experience' => '2-3 ans',
                    'salaire_min' => 55000,
                    'salaire_max' => 78000,
                    'type_id' => 2,
                    'categorie_id' => 3,
                ],
            ];

            foreach ($jobs as $job) {
                if ($job['categorie_id'] == $entreprise->id % 3 + 1 || true) { // Juste pour varier un peu
                    Offre::create(array_merge($job, [
                        'entreprise_id' => $entreprise->id,
                        'slug' => Str::slug($job['titre'] . '-' . uniqid()),
                        'date_fin' => now()->addDays(30),
                        'start_date' => 'Dès que possible',
                        'employment_type' => 'Permanent',
                        'remote_work' => 'Hybride',
                    ]));
                }
            }
        }
    }
}
