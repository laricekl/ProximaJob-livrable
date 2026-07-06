<?php

namespace App\Console\Commands;

use App\Models\CvProfile;
use App\Models\CvFormation;
use App\Models\CvCompetence;
use App\Models\CvExperience;
use App\Models\CvLangue;
use App\Models\CvPerfectionnement;
use App\Models\CvBenevolat;
use App\Models\Entreprise;
use App\Models\Notification;
use App\Models\Offre;
use App\Models\Postulation;
use App\Models\Sector;
use App\Models\TypeOffre;
use App\Models\User;
use App\Models\UserAbonnement;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedDemoData extends Command
{
    protected $signature = 'demo:seed';
    protected $description = 'Remplit les comptes demo avec des donnees riches pour presentation';

    public function handle(): int
    {
        $this->info('🌱 Remplissage des données de démo...');

        $this->seedCandidate();
        $this->seedEnterprise();
        $this->seedOffers();
        $this->seedApplications();
        $this->seedNotifications();
        $this->seedSubscription();

        $this->newLine();
        $this->info('✅ Données de démo prêtes !');
        $this->table(
            ['Compte', 'Email', 'Mot de passe', 'Rôle'],
            [
                ['Candidat', 'test@example.com', 'password', 'candidat'],
                ['Entreprise', 'contact@techsolutions.com', 'password', 'entreprise'],
            ]
        );

        return self::SUCCESS;
    }

    private function seedCandidate(): void
    {
        $this->info('  👤 Candidat...');

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Dupont',
                'prenom' => 'Thomas',
                'telephone' => '514-555-0123',
                'adresse' => '123 Rue Sherbrooke, Montreal, QC',
                'password' => bcrypt('password'),
                'status' => 'Actif',
                'is_active' => true,
                'email_verified_at' => now(),
                'salary_expectation_min' => 65000,
            ]
        );

        $user->syncRoles('candidat');

        // CV Profile complet
        $cvProfile = CvProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nom' => 'Dupont',
                'prenom' => 'Thomas',
                'email' => 'test@example.com',
                'telephone' => '514-555-0123',
                'adresse' => '123 Rue Sherbrooke',
                'ville' => 'Montreal (Quebec)',
                'code_postal' => 'H2X 1C2',
                'langues_competences' => 'Francais (courant), Anglais (professionnel), Espagnol (notions)',
                'logiciels' => 'VS Code, Docker, Git, Figma, JIRA, Notion, PostgreSQL, Excel avancé',
            ]
        );

        // Formations
        CvFormation::where('cv_profile_id', $cvProfile->id)->delete();
        CvFormation::create([
            'cv_profile_id' => $cvProfile->id, 'periode' => '2018-2021',
            'diplome' => 'Baccalauréat en informatique', 'diplome_id' => 1,
            'etablissement' => 'Université de Montréal, Montréal (Québec)', 'ordre' => 1,
        ]);
        CvFormation::create([
            'cv_profile_id' => $cvProfile->id, 'periode' => '2016-2018',
            'diplome' => 'DEC Techniques de l\'informatique', 'diplome_id' => 2,
            'etablissement' => 'Cégep du Vieux Montréal, Montréal (Québec)', 'ordre' => 2,
        ]);

        // Compétences (table simplifiée : description, type, ordre)
        $skills = [
            ['description' => 'Laravel (Expert, 5 ans)', 'type' => 'technique'],
            ['description' => 'React / Vue.js (Avancé, 4 ans)', 'type' => 'technique'],
            ['description' => 'Tailwind CSS (Expert, 3 ans)', 'type' => 'technique'],
            ['description' => 'MySQL / PostgreSQL (Avancé, 5 ans)', 'type' => 'technique'],
            ['description' => 'Docker (Intermédiaire, 3 ans)', 'type' => 'technique'],
            ['description' => 'Git / CI-CD (Avancé, 4 ans)', 'type' => 'technique'],
            ['description' => 'Python (Intermédiaire, 2 ans)', 'type' => 'technique'],
            ['description' => 'Gestion de projet agile (Avancé, 4 ans)', 'type' => 'generale'],
        ];
        CvCompetence::where('cv_profile_id', $cvProfile->id)->delete();
        foreach ($skills as $i => $skill) {
            CvCompetence::create(array_merge($skill, [
                'cv_profile_id' => $cvProfile->id,
                'ordre' => $i + 1,
            ]));
        }

        // Expériences
        CvExperience::where('cv_profile_id', $cvProfile->id)->delete();
        CvExperience::create([
            'cv_profile_id' => $cvProfile->id, 'ordre' => 1,
            'periode' => '2023-03 - Aujourd\'hui', 'poste' => 'Développeur Full Stack Senior',
            'entreprise' => 'Tech Solutions Inc.',
            'description' => "Conception et développement d'une plateforme SaaS de matching emploi-candidat avec IA.\n- Architecture Laravel + React\n- Intégration API Gemini pour génération de CV personnalisés\n- Gestion d'équipe de 4 développeurs\n- Mise en place CI/CD avec GitHub Actions\n- Optimisation des performances (temps de réponse divisé par 3)",
        ]);
        CvExperience::create([
            'cv_profile_id' => $cvProfile->id, 'ordre' => 2,
            'periode' => '2021-01 - 2023-02', 'poste' => 'Développeur Web',
            'entreprise' => 'InnovaGroup',
            'description' => "Développement d'applications web sur mesure pour clients entreprises.\n- Stack PHP/Laravel et JavaScript/Vue.js\n- Création d'API RESTful\n- Maintenance et évolution d'un portail client (50K utilisateurs)",
        ]);
        CvExperience::create([
            'cv_profile_id' => $cvProfile->id, 'ordre' => 3,
            'periode' => '2019-06 - 2020-12', 'poste' => 'Développeur Junior',
            'entreprise' => 'Startup Web Inc.',
            'description' => "Première expérience en développement web.\n- PHP, MySQL, JavaScript\n- Développement de sites e-commerce\n- Support technique niveau 2",
        ]);

        // Langues
        CvLangue::where('cv_profile_id', $cvProfile->id)->delete();
        $langues = [
            ['nom' => 'Français', 'niveau' => 'Langue maternelle'],
            ['nom' => 'Anglais', 'niveau' => 'Professionnel'],
            ['nom' => 'Espagnol', 'niveau' => 'Notions'],
        ];
        foreach ($langues as $i => $langue) {
            CvLangue::create(array_merge($langue, ['cv_profile_id' => $cvProfile->id, 'ordre' => $i + 1]));
        }

        // Perfectionnements
        CvPerfectionnement::where('cv_profile_id', $cvProfile->id)->delete();
        CvPerfectionnement::create([
            'cv_profile_id' => $cvProfile->id, 'ordre' => 1, 'annee' => '2024',
            'formation' => 'Certification AWS Cloud Practitioner', 'etablissement' => 'AWS Training, Montréal',
        ]);
        CvPerfectionnement::create([
            'cv_profile_id' => $cvProfile->id, 'ordre' => 2, 'annee' => '2023',
            'formation' => 'Formation Docker & Kubernetes', 'etablissement' => 'Udemy, En ligne',
        ]);

        // Bénévolat
        CvBenevolat::where('cv_profile_id', $cvProfile->id)->delete();
        CvBenevolat::create([
            'cv_profile_id' => $cvProfile->id, 'ordre' => 1, 'periode' => '2023-2024',
            'role' => 'Mentor en programmation', 'organisation' => 'Les Hacktivateurs',
        ]);

        // Secteur candidat
        $sector = Sector::where('slug', 'technologie-et-informatique')->first();
        if ($sector) {
            \App\Models\CandidateSector::updateOrCreate(
                ['user_id' => $user->id],
                ['sector_id' => $sector->id]
            );
        }
    }

    private function seedEnterprise(): void
    {
        $this->info('  🏢 Entreprise...');

        $user = User::firstOrCreate(
            ['email' => 'contact@techsolutions.com'],
            [
                'name' => 'Laurent',
                'prenom' => 'Marie',
                'telephone' => '514-555-9800',
                'adresse' => '456 Boul. René-Lévesque, Montreal, QC',
                'password' => bcrypt('password'),
                'status' => 'Actif',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles('entreprise');

        $entreprise = Entreprise::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => 'Tech Solutions Inc.',
                'description' => 'Tech Solutions est une entreprise montréalaise spécialisée dans le développement de solutions logicielles sur mesure. Nous accompagnons nos clients dans leur transformation numérique depuis 2015.',
                'website' => 'https://techsolutions.example.com',
                'neq' => 'NEQ-1234567890',
                'status' => 'approved',
                'verified_at' => now(),
            ]
        );
    }

    private function seedOffers(): void
    {
        $this->info('  📋 Offres d\'emploi...');

        $entreprise = Entreprise::where('company_name', 'Tech Solutions Inc.')->first();
        if (!$entreprise) return;

        $types = TypeOffre::pluck('id', 'nom');
        $sector = Sector::where('slug', 'technologie-et-informatique')->first();

        $offers = [
            [
                'titre' => 'Développeur Full Stack Laravel/React',
                'description' => "Rejoignez notre équipe produit pour développer la prochaine génération de notre plateforme SaaS. Vous travaillerez sur des fonctionnalités innovantes utilisant l'intelligence artificielle pour automatiser le matching entre candidats et offres d'emploi.\n\nEnvironnement technologique : Laravel 12, React 19, Tailwind CSS, PostgreSQL, Docker.",
                'responsibilities' => "Concevoir et développer des fonctionnalités full stack\nParticiper aux code reviews et à l'amélioration continue\nCollaborer avec l'équipe produit sur les spécifications\nMentorer les développeurs juniors\nOptimiser les performances et la sécurité",
                'criteres' => 'Laravel, React, PostgreSQL, Docker, Git, API REST',
                'education_level' => 'Universitaire 1er cycle',
                'localisation' => 'Montréal, QC',
                'salaire_min' => 75000,
                'salaire_max' => 100000,
                'type_id' => $types['Temps plein'] ?? 1,
                'remote_work' => 'Hybride',
                'secteur_id' => $sector?->id,
            ],
            [
                'titre' => 'Développeur Frontend React Senior',
                'description' => "Nous recherchons un développeur frontend passionné pour rejoindre notre équipe produit. Vous serez responsable de l'expérience utilisateur de notre plateforme utilisée par des milliers de chercheurs d'emploi.",
                'responsibilities' => "Développer des interfaces utilisateur réactives en React\nCréer des composants réutilisables avec Tailwind CSS\nCollaborer avec les designers UX/UI\nAssurer l'accessibilité et la performance frontend",
                'competences' => 'React, TypeScript, Tailwind CSS, Figma, Jest, Cypress',
                'localisation' => 'Montréal, QC',
                'salaire_min' => 70000,
                'salaire_max' => 95000,
                'type_id' => $types['Temps plein'] ?? 1,
                'remote_work' => 'Hybride',
                'secteur_id' => $sector?->id,
            ],
            [
                'titre' => 'DevOps Engineer',
                'description' => "Vous mettrez en place et maintiendrez l'infrastructure cloud de notre plateforme. Vous automatiserez les déploiements et assurerez la fiabilité de nos services en production.",
                'responsibilities' => "Gérer l'infrastructure AWS\nAutomatiser les pipelines CI/CD\nSuperviser la sécurité et les performances\nMettre en place le monitoring et les alertes",
                'competences' => 'AWS, Docker, Kubernetes, Terraform, GitHub Actions, Linux',
                'localisation' => 'Montréal, QC',
                'salaire_min' => 80000,
                'salaire_max' => 110000,
                'type_id' => $types['Temps plein'] ?? 1,
                'remote_work' => 'Télétravail',
                'secteur_id' => $sector?->id,
            ],
            [
                'titre' => 'UX/UI Designer',
                'description' => "Façonnez l'expérience utilisateur de notre plateforme. Vous travaillerez en étroite collaboration avec les développeurs et le chef de produit pour créer des interfaces intuitives et modernes.",
                'responsibilities' => "Concevoir des wireframes et prototypes\nRéaliser des tests utilisateurs\nCréer et maintenir le design system\nCollaborer avec les développeurs sur l'implémentation",
                'competences' => 'Figma, Design System, User Research, Prototypage, HTML/CSS',
                'localisation' => 'Montréal, QC',
                'salaire_min' => 65000,
                'salaire_max' => 85000,
                'type_id' => $types['Temps plein'] ?? 1,
                'remote_work' => 'Hybride',
                'secteur_id' => $sector?->id,
            ],
            [
                'titre' => 'Chef de Projet TI',
                'description' => "Pilotez des projets de développement logiciel de A à Z. Vous serez le point de contact principal entre les clients et l'équipe technique, assurant la livraison dans les délais et le respect du budget.",
                'responsibilities' => "Gérer le cycle de vie complet des projets\nAnimer les cérémonies agiles (daily, sprint planning, rétro)\nAssurer la communication avec les parties prenantes\nSuivre les indicateurs de performance",
                'competences' => 'Scrum, JIRA, Gestion de projet, Communication, Budget',
                'localisation' => 'Montréal, QC',
                'salaire_min' => 70000,
                'salaire_max' => 90000,
                'type_id' => $types['Temps plein'] ?? 1,
                'remote_work' => 'Présentiel',
                'secteur_id' => $sector?->id,
            ],
        ];

        foreach ($offers as $data) {
            $data['entreprise_id'] = $entreprise->id;
            $data['slug'] = Str::slug($data['titre'] . '-' . Str::random(4));
            $data['poste'] = $data['titre'];
            $data['status'] = 'active';
            $data['date_fin'] = now()->addMonths(2);
            $data['salary_type'] = 'annuel';
            $data['start_date'] = 'Sous 1 mois';
            $data['experience'] = '2-3 ans';
            $data['langues'] = 'Français courant, Anglais professionnel';
            $data['missions'] = $data['description'];
            $data['criteres'] = $data['competences'] ?? '';
            $data['categorie_id'] = 1;
            unset($data['competences']);

            Offre::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }

    private function seedApplications(): void
    {
        $this->info('  📨 Candidatures...');

        $candidate = User::where('email', 'test@example.com')->first();
        $offers = Offre::where('status', 'active')->take(5)->get();

        if (!$candidate || $offers->isEmpty()) return;

        $statuses = ['en_attente', 'en_attente', 'accepted', 'rejected', 'en_attente'];
        $types = [false, false, true, true, false]; // autopostulation

        foreach ($offers as $i => $offer) {
            $exists = Postulation::where('user_id', $candidate->id)
                ->where('offre_id', $offer->id)
                ->exists();
            if ($exists) continue;

            Postulation::create([
                'user_id' => $candidate->id,
                'offre_id' => $offer->id,
                'status' => $statuses[$i] ?? 'en_attente',
                'autopostulation' => $types[$i] ?? false,
                'cv' => 'assets/cvs/demo/candidate-cv-demo.pdf',
                'lettre_motivation' => ($i === 0 || $i === 2) ? 'assets/cvs/demo/candidate-letter-demo.pdf' : null,
                'match_score' => [85, 92, 78, 45, 88][$i] ?? 80,
                'match_details' => json_encode([
                    'competences_techniques' => [80, 90, 75, 40, 85][$i] ?? 80,
                    'competences_generales' => [85, 95, 80, 50, 90][$i] ?? 80,
                    'experience' => [90, 90, 80, 45, 90][$i] ?? 80,
                ]),
            ]);
        }
    }

    private function seedNotifications(): void
    {
        $this->info('  🔔 Notifications...');

        $candidate = User::where('email', 'test@example.com')->first();

        if (!$candidate) return;

        $notifications = [
            [
                'title' => '🎉 Félicitations !',
                'message' => 'Votre candidature pour "Développeur Full Stack Laravel/React" a été acceptée. L\'entreprise souhaite vous rencontrer.',
                'role' => 'candidat',
                'link' => '/user/historique-candidatures',
                'is_read' => false,
            ],
            [
                'title' => 'Nouveau matching IA',
                'message' => 'Votre profil correspond à 5 nouvelles offres. Consultez vos suggestions personnalisées.',
                'role' => 'candidat',
                'link' => '/user/historique-candidatures_ia',
                'is_read' => false,
            ],
            [
                'title' => 'CV personnalisé généré',
                'message' => 'Votre CV personnalisé pour l\'offre "DevOps Engineer" est prêt. Vous pouvez le consulter et le télécharger.',
                'role' => 'candidat',
                'link' => '/user/historique-candidatures_ia',
                'is_read' => false,
            ],
            [
                'title' => 'Rappel : complétez votre profil',
                'message' => 'Les profils avec une photo et des compétences reçoivent 3x plus de sollicitations.',
                'role' => 'candidat',
                'link' => '/user/profil-public',
                'is_read' => true,
            ],
            [
                'title' => 'Mise à jour de votre candidature',
                'message' => 'Votre candidature pour "UX/UI Designer" est en cours d\'examen par l\'entreprise.',
                'role' => 'candidat',
                'link' => '/user/historique-candidatures',
                'is_read' => true,
            ],
        ];

        foreach ($notifications as $notif) {
            Notification::updateOrCreate(
                [
                    'user_id' => $candidate->id,
                    'title' => $notif['title'],
                ],
                $notif
            );
        }
    }

    private function seedSubscription(): void
    {
        $this->info('  ⭐ Abonnement...');

        $candidate = User::where('email', 'test@example.com')->first();
        if (!$candidate) return;

        UserAbonnement::updateOrCreate(
            ['user_id' => $candidate->id, 'status' => 'Actif'],
            [
                'abonnement_id' => 2,
                'date_debut' => now()->subDays(10),
                'date_fin' => now()->addDays(20),
                'status' => 'Actif',
            ]
        );
    }
}
