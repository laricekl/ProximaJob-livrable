<?php

namespace Database\Seeders;

use App\Models\Abonnement;
use App\Models\CandidateSector;
use App\Models\Categorie;
use App\Models\CvProfile;
use App\Models\Diplome;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Sector;
use App\Models\TypeOffre;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $types = $this->seedTypes();
        $sectors = $this->seedSectors();
        $diplomes = $this->seedDiplomes();
        $this->seedAbonnements();

        $entreprises = $this->seedEntreprises();
        $this->seedCandidates($sectors, $diplomes);
        $this->seedOffers($entreprises, $categories, $types, $sectors);
    }

    private function seedCategories(): array
    {
        $categories = [
            'tech' => ['nom' => 'Informatique & Technologie', 'couleur' => '#3b82f6', 'icone' => 'fas fa-laptop-code'],
            'health' => ['nom' => 'Sante & Social', 'couleur' => '#ef4444', 'icone' => 'fas fa-user-md'],
            'education' => ['nom' => 'Education', 'couleur' => '#10b981', 'icone' => 'fas fa-graduation-cap'],
            'marketing' => ['nom' => 'Vente & Marketing', 'couleur' => '#f59e0b', 'icone' => 'fas fa-shopping-cart'],
            'finance' => ['nom' => 'Finance', 'couleur' => '#6366f1', 'icone' => 'fas fa-money-bill-wave'],
        ];

        foreach ($categories as $key => $payload) {
            $categories[$key] = Categorie::updateOrCreate(['nom' => $payload['nom']], $payload);
        }

        return $categories;
    }

    private function seedTypes(): array
    {
        $types = [
            'full_time' => ['id' => 1, 'nom' => 'Temps plein'],
            'part_time' => ['id' => 2, 'nom' => 'Temps partiel'],
            'contract' => ['id' => 3, 'nom' => 'Contractuel'],
            'internship' => ['id' => 4, 'nom' => 'Stage'],
        ];

        foreach ($types as $key => $payload) {
            $types[$key] = TypeOffre::updateOrCreate(['id' => $payload['id']], $payload);
        }

        return $types;
    }

    private function seedSectors(): array
    {
        $sectors = [
            'tech' => ['name' => 'Technologie et informatique', 'slug' => 'technologie-et-informatique', 'is_active' => true],
            'health' => ['name' => 'Sante et services sociaux', 'slug' => 'sante-et-services-sociaux', 'is_active' => true],
            'education' => ['name' => 'Education et formation', 'slug' => 'education-et-formation', 'is_active' => true],
            'marketing' => ['name' => 'Marketing et communication', 'slug' => 'marketing-et-communication', 'is_active' => true],
            'finance' => ['name' => 'Finance et comptabilite', 'slug' => 'finance-et-comptabilite', 'is_active' => true],
        ];

        foreach ($sectors as $key => $payload) {
            $sectors[$key] = Sector::updateOrCreate(['slug' => $payload['slug']], $payload);
        }

        return $sectors;
    }

    private function seedDiplomes(): array
    {
        $diplomes = [
            'bac_info' => [
                'nom_diplome' => 'Baccalaureat en informatique',
                'nom_anglais' => 'Bachelor in Computer Science',
                'sigle' => 'BAC INFO',
                'niveau_education' => Diplome::NIVEAU_UNIVERSITAIRE_1ER_CYCLE,
                'duree_annees' => 3,
                'statut' => Diplome::STATUT_ACTIF,
            ],
            'dec_tech' => [
                'nom_diplome' => 'DEC Techniques de l informatique',
                'nom_anglais' => 'College Diploma in IT',
                'sigle' => 'DEC TI',
                'niveau_education' => Diplome::NIVEAU_COLLEGIAL,
                'duree_annees' => 3,
                'statut' => Diplome::STATUT_ACTIF,
            ],
            'bac_admin' => [
                'nom_diplome' => 'Baccalaureat en administration',
                'nom_anglais' => 'Bachelor in Business Administration',
                'sigle' => 'BBA',
                'niveau_education' => Diplome::NIVEAU_UNIVERSITAIRE_1ER_CYCLE,
                'duree_annees' => 3,
                'statut' => Diplome::STATUT_ACTIF,
            ],
            'dep_sante' => [
                'nom_diplome' => 'Diplome d etudes professionnelles en sante',
                'nom_anglais' => 'Vocational Diploma in Health',
                'sigle' => 'DEP SANTE',
                'niveau_education' => Diplome::NIVEAU_PROFESSIONNEL,
                'duree_annees' => 2,
                'statut' => Diplome::STATUT_ACTIF,
            ],
        ];

        foreach ($diplomes as $key => $payload) {
            $diplomes[$key] = Diplome::updateOrCreate(
                ['nom_diplome' => $payload['nom_diplome']],
                $payload
            );
        }

        return $diplomes;
    }

    private function seedAbonnements(): void
    {
        $plans = [
            [
                'nom' => 'Decouverte',
                'duree' => 'mensuel',
                'montant' => 0,
                'description' => 'Pour explorer la plateforme et tester les bases.',
                'couleur' => '#94a3b8',
                'populaire' => false,
                'actif' => true,
            ],
            [
                'nom' => 'Pro',
                'duree' => 'mensuel',
                'montant' => 49,
                'description' => 'Pour publier et gerer plusieurs recrutements.',
                'couleur' => '#f97316',
                'populaire' => true,
                'actif' => true,
            ],
            [
                'nom' => 'Entreprise',
                'duree' => 'annuel',
                'montant' => 399,
                'description' => 'Pour une equipe avec suivi et visibilite etendue.',
                'couleur' => '#0f172a',
                'populaire' => false,
                'actif' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Abonnement::updateOrCreate(['nom' => $plan['nom']], $plan);
        }
    }

    private function seedEntreprises(): array
    {
        $items = [
            [
                'email' => 'contact@techsolutions.com',
                'prenom' => 'Sophie',
                'name' => 'Tech Solutions',
                'telephone' => '514-555-1101',
                'company_name' => 'Tech Solutions Inc.',
                'description' => 'Equipe produit orientee SaaS, plateforme web et automatisation RH.',
                'website' => 'https://techsolutions.com',
                'neq' => '1176543210',
            ],
            [
                'email' => 'info@globalhealth.com',
                'prenom' => 'Marc',
                'name' => 'Global Health',
                'telephone' => '418-555-2040',
                'company_name' => 'Global Health Group',
                'description' => 'Organisation sante et services avec besoins en clinique et operations.',
                'website' => 'https://globalhealth.com',
                'neq' => '1176543211',
            ],
            [
                'email' => 'talents@futureacademy.ca',
                'prenom' => 'Nadia',
                'name' => 'Future Academy',
                'telephone' => '819-555-3300',
                'company_name' => 'Future Education Academy',
                'description' => 'Reseau educatif axe numerique, innovation et pedagogie pratique.',
                'website' => 'https://futureacademy.ca',
                'neq' => '1176543212',
            ],
        ];

        $entreprises = [];

        foreach ($items as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'prenom' => $item['prenom'],
                    'name' => $item['name'],
                    'telephone' => $item['telephone'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'status' => 'Actif',
                    'is_active' => true,
                ]
            );

            $user->syncRoles(['entreprise']);

            $entreprises[$item['email']] = Entreprise::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $item['company_name'],
                    'description' => $item['description'],
                    'website' => $item['website'],
                    'neq' => $item['neq'],
                    'status' => 'approved',
                    'verified_at' => now(),
                ]
            );
        }

        return $entreprises;
    }

    private function seedCandidates(array $sectors, array $diplomes): void
    {
        $candidates = [
            [
                'email' => 'test@example.com',
                'prenom' => 'Jean',
                'name' => 'Martin',
                'telephone' => '514-555-7788',
                'adresse' => '1200 Rue Sainte-Catherine Ouest',
                'sector' => $sectors['tech'],
                'diplome' => $diplomes['bac_info'],
                'experience_years' => 4,
                'profile' => [
                    'ville' => 'Montreal',
                    'code_postal' => 'H3B 1K1',
                    'province' => 'QC',
                    'langues_competences' => 'Francais, Anglais',
                    'logiciels' => 'Laravel, PHP, SQL, Git',
                ],
            ],
            [
                'email' => 'camille.roy@example.com',
                'prenom' => 'Camille',
                'name' => 'Roy',
                'telephone' => '438-555-6611',
                'adresse' => '455 Boulevard Rene-Levesque Est',
                'sector' => $sectors['marketing'],
                'diplome' => $diplomes['bac_admin'],
                'experience_years' => 3,
                'profile' => [
                    'ville' => 'Montreal',
                    'code_postal' => 'H2L 0A6',
                    'province' => 'QC',
                    'langues_competences' => 'Francais, Anglais',
                    'logiciels' => 'HubSpot, Notion, Canva, Google Analytics',
                ],
            ],
        ];

        foreach ($candidates as $candidate) {
            $user = User::updateOrCreate(
                ['email' => $candidate['email']],
                [
                    'prenom' => $candidate['prenom'],
                    'name' => $candidate['name'],
                    'telephone' => $candidate['telephone'],
                    'adresse' => $candidate['adresse'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'status' => 'Actif',
                    'is_active' => true,
                ]
            );

            $user->syncRoles(['candidat']);

            CvProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nom' => $candidate['name'],
                    'prenom' => $candidate['prenom'],
                    'email' => $candidate['email'],
                    'telephone' => $candidate['telephone'],
                    'adresse' => $candidate['adresse'],
                    'ville' => $candidate['profile']['ville'],
                    'code_postal' => $candidate['profile']['code_postal'],
                    'province' => $candidate['profile']['province'],
                    'langues_competences' => $candidate['profile']['langues_competences'],
                    'logiciels' => $candidate['profile']['logiciels'],
                ]
            );

            CandidateSector::updateOrCreate(
                ['candidate_id' => $user->id],
                [
                    'sector_id' => $candidate['sector']->id,
                    'diplome_id' => $candidate['diplome']->id,
                    'experience_years' => $candidate['experience_years'],
                ]
            );
        }
    }

    private function seedOffers(array $entreprises, array $categories, array $types, array $sectors): void
    {
        $offers = [
            [
                'entreprise' => $entreprises['contact@techsolutions.com'],
                'titre' => 'Developpeur Fullstack Laravel',
                'poste' => 'Developpeur Fullstack Laravel',
                'description' => 'Participation au developpement des modules candidats, entreprises et administration.',
                'localisation' => 'Montreal, QC',
                'categorie' => $categories['tech'],
                'sector' => $sectors['tech'],
                'type' => $types['full_time'],
                'experience' => '3 a 5 ans',
                'required_experience' => '3 ans minimum',
                'salaire_min' => 78000,
                'salaire_max' => 105000,
                'employment_type' => 'Permanent',
                'remote_work' => 'Hybride',
                'job_category' => 'informatique',
                'salary_type' => 'annuel',
                'education_level' => 'Universitaire',
                'responsibilities' => 'Concevoir, livrer et maintenir des fonctionnalites Laravel robustes.',
            ],
            [
                'entreprise' => $entreprises['contact@techsolutions.com'],
                'titre' => 'Analyste QA Produit',
                'poste' => 'Analyste QA Produit',
                'description' => 'Mise en place des scenarios de tests fonctionnels et suivi qualite sur les parcours publics.',
                'localisation' => 'Montreal, QC',
                'categorie' => $categories['tech'],
                'sector' => $sectors['tech'],
                'type' => $types['contract'],
                'experience' => '2 a 4 ans',
                'required_experience' => '2 ans minimum',
                'salaire_min' => 68000,
                'salaire_max' => 90000,
                'employment_type' => 'Contrat',
                'remote_work' => 'Remote',
                'job_category' => 'qualite',
                'salary_type' => 'annuel',
                'education_level' => 'Collegial ou universitaire',
                'responsibilities' => 'Documenter les anomalies et proteger les parcours critiques du site.',
            ],
            [
                'entreprise' => $entreprises['info@globalhealth.com'],
                'titre' => 'Infirmier clinicien',
                'poste' => 'Infirmier clinicien',
                'description' => 'Prise en charge clinique et coordination avec les equipes terrain.',
                'localisation' => 'Quebec, QC',
                'categorie' => $categories['health'],
                'sector' => $sectors['health'],
                'type' => $types['full_time'],
                'experience' => '1 a 3 ans',
                'required_experience' => '1 an minimum',
                'salaire_min' => 62000,
                'salaire_max' => 86000,
                'employment_type' => 'Permanent',
                'remote_work' => 'Sur site',
                'job_category' => 'sante',
                'salary_type' => 'annuel',
                'education_level' => 'Professionnel',
                'responsibilities' => 'Assurer le suivi patient et la qualite des interventions.',
            ],
            [
                'entreprise' => $entreprises['talents@futureacademy.ca'],
                'titre' => 'Conseiller pedagogique numerique',
                'poste' => 'Conseiller pedagogique numerique',
                'description' => 'Accompagnement des equipes enseignantes dans l integration d outils numeriques.',
                'localisation' => 'Sherbrooke, QC',
                'categorie' => $categories['education'],
                'sector' => $sectors['education'],
                'type' => $types['part_time'],
                'experience' => '2 a 5 ans',
                'required_experience' => '2 ans minimum',
                'salaire_min' => 54000,
                'salaire_max' => 76000,
                'employment_type' => 'Temps partiel',
                'remote_work' => 'Hybride',
                'job_category' => 'education',
                'salary_type' => 'annuel',
                'education_level' => 'Universitaire',
                'responsibilities' => 'Structurer les parcours de formation et soutenir l adoption des outils.',
            ],
            [
                'entreprise' => $entreprises['talents@futureacademy.ca'],
                'titre' => 'Charge de contenu formation',
                'poste' => 'Charge de contenu formation',
                'description' => 'Creation de contenu pedagogique clair pour programmes hybrides et modules courts.',
                'localisation' => 'Laval, QC',
                'categorie' => $categories['marketing'],
                'sector' => $sectors['marketing'],
                'type' => $types['internship'],
                'experience' => 'Stage ou premiere experience',
                'required_experience' => 'Aisance redactionnelle',
                'salaire_min' => 22000,
                'salaire_max' => 32000,
                'employment_type' => 'Stage',
                'remote_work' => 'Hybride',
                'job_category' => 'contenu',
                'salary_type' => 'annuel',
                'education_level' => 'Collegial ou universitaire',
                'responsibilities' => 'Rediger, adapter et mettre a jour des contenus de formation.',
            ],
        ];

        foreach ($offers as $index => $offer) {
            Offre::updateOrCreate(
                [
                    'entreprise_id' => $offer['entreprise']->id,
                    'titre' => $offer['titre'],
                ],
                [
                    'poste' => $offer['poste'],
                    'description' => $offer['description'],
                    'localisation' => $offer['localisation'],
                    'categorie_id' => $offer['categorie']->id,
                    'sector_id' => $offer['sector']->id,
                    'type_id' => $offer['type']->id,
                    'status' => 'active',
                    'experience' => $offer['experience'],
                    'salaire_min' => $offer['salaire_min'],
                    'salaire_max' => $offer['salaire_max'],
                    'slug' => Str::slug($offer['titre'].'-'.$offer['entreprise']->company_name.'-'.($index + 1)),
                    'langues' => 'Francais, Anglais',
                    'date_fin' => now()->addDays(45)->toDateString(),
                    'employment_type' => $offer['employment_type'],
                    'remote_work' => $offer['remote_work'],
                    'job_category' => $offer['job_category'],
                    'salary_type' => $offer['salary_type'],
                    'start_date' => 'Des que possible',
                    'required_experience' => $offer['required_experience'],
                    'education_level' => $offer['education_level'],
                    'responsibilities' => $offer['responsibilities'],
                ]
            );
        }
    }
}
