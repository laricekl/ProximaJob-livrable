<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sector;
use App\Models\Skill;
use App\Models\SectorSkill;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ImportScianSkills extends Command
{
    protected $signature = 'scian:import-skills {--force : Force l\'import même si des données existent}';
    protected $description = 'Importe les compétences liées aux secteurs SCIAN basées sur la CNP 2021';

    public function handle()
    {
        $this->info('🔧 Import des compétences SCIAN-CNP...');

        // Vérifier si des données existent déjà
        if (Skill::count() > 0 && !$this->option('force')) {
            if (!$this->confirm('Des compétences existent déjà. Continuer l\'import ?')) {
                $this->info('Import annulé.');
                return;
            }
        }

        try {
            // Créer les tables nécessaires
            $this->createSkillsTables();
            
            // Import des compétences de base
            $this->importCoreSkills();
            
            // Mapping des compétences aux secteurs
            $this->mapSkillsToSectors();
            
            $this->info('✅ Import des compétences SCIAN terminé avec succès !');
            $this->table(
                ['Compétences totales', 'Associations secteur-compétences'], 
                [[Skill::count(), SectorSkill::count()]]
            );
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'import : ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function createSkillsTables()
    {
        // Table des compétences
        if (!\Schema::hasTable('skills')) {
            \Schema::create('skills', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('category'); // technique, transversale, cognitive, etc.
                $table->string('cnp_code')->nullable(); // Référence CNP
                $table->integer('importance_level')->default(1); // 1-5
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['category', 'is_active']);
                $table->index('cnp_code');
            });
            $this->info('✓ Table skills créée');
        }

        // Table de liaison secteur-compétences
        if (!\Schema::hasTable('sector_skills')) {
            \Schema::create('sector_skills', function ($table) {
                $table->id();
                $table->foreignId('sector_id')->constrained()->onDelete('cascade');
                $table->foreignId('skill_id')->constrained()->onDelete('cascade');
                $table->integer('relevance_score')->default(3); // 1-5 (pertinence)
                $table->boolean('is_core_skill')->default(false); // Compétence clé du secteur
                $table->timestamps();
                
                $table->unique(['sector_id', 'skill_id']);
                $table->index(['sector_id', 'is_core_skill']);
            });
            $this->info('✓ Table sector_skills créée');
        }
    }

    private function importCoreSkills()
    {
        $this->info('📚 Import des compétences de base...');

        $coreSkills = [
            // Compétences techniques - Construction (23)
            ['name' => 'Lecture de plans et devis', 'category' => 'technique', 'cnp_code' => '7271', 'importance' => 5],
            ['name' => 'Utilisation d\'outils électriques', 'category' => 'technique', 'cnp_code' => '7271', 'importance' => 4],
            ['name' => 'Soudure', 'category' => 'technique', 'cnp_code' => '7237', 'importance' => 4],
            ['name' => 'Maçonnerie', 'category' => 'technique', 'cnp_code' => '7281', 'importance' => 4],
            ['name' => 'Plomberie', 'category' => 'technique', 'cnp_code' => '7251', 'importance' => 4],
            ['name' => 'Électricité', 'category' => 'technique', 'cnp_code' => '7241', 'importance' => 5],
            
            // Compétences techniques - Santé (62)
            ['name' => 'Soins infirmiers', 'category' => 'technique', 'cnp_code' => '3012', 'importance' => 5],
            ['name' => 'Diagnostic médical', 'category' => 'technique', 'cnp_code' => '3111', 'importance' => 5],
            ['name' => 'Premiers secours', 'category' => 'technique', 'cnp_code' => '3012', 'importance' => 4],
            ['name' => 'Pharmacologie', 'category' => 'technique', 'cnp_code' => '3131', 'importance' => 4],
            ['name' => 'Radiologie', 'category' => 'technique', 'cnp_code' => '3215', 'importance' => 4],
            ['name' => 'Physiothérapie', 'category' => 'technique', 'cnp_code' => '3142', 'importance' => 4],
            
            // Compétences techniques - Services professionnels (54)
            ['name' => 'Droit commercial', 'category' => 'technique', 'cnp_code' => '4112', 'importance' => 5],
            ['name' => 'Comptabilité', 'category' => 'technique', 'cnp_code' => '1111', 'importance' => 5],
            ['name' => 'Programmation', 'category' => 'technique', 'cnp_code' => '2174', 'importance' => 5],
            ['name' => 'Architecture', 'category' => 'technique', 'cnp_code' => '2151', 'importance' => 5],
            ['name' => 'Génie civil', 'category' => 'technique', 'cnp_code' => '2131', 'importance' => 5],
            ['name' => 'Design graphique', 'category' => 'technique', 'cnp_code' => '5241', 'importance' => 4],
            
            // Compétences techniques - Fabrication (31-33)
            ['name' => 'Usinage', 'category' => 'technique', 'cnp_code' => '7231', 'importance' => 4],
            ['name' => 'Contrôle qualité', 'category' => 'technique', 'cnp_code' => '2264', 'importance' => 4],
            ['name' => 'Maintenance industrielle', 'category' => 'technique', 'cnp_code' => '7311', 'importance' => 4],
            ['name' => 'Automatisation', 'category' => 'technique', 'cnp_code' => '2241', 'importance' => 4],
            ['name' => 'Transformation alimentaire', 'category' => 'technique', 'cnp_code' => '9461', 'importance' => 3],
            
            // Compétences transversales
            ['name' => 'Communication orale', 'category' => 'transversale', 'cnp_code' => null, 'importance' => 5],
            ['name' => 'Communication écrite', 'category' => 'transversale', 'cnp_code' => null, 'importance' => 4],
            ['name' => 'Travail d\'équipe', 'category' => 'transversale', 'cnp_code' => null, 'importance' => 5],
            ['name' => 'Leadership', 'category' => 'transversale', 'cnp_code' => null, 'importance' => 4],
            ['name' => 'Résolution de problèmes', 'category' => 'transversale', 'cnp_code' => null, 'importance' => 5],
            ['name' => 'Gestion du temps', 'category' => 'transversale', 'cnp_code' => null, 'importance' => 4],
            ['name' => 'Adaptabilité', 'category' => 'transversale', 'cnp_code' => null, 'importance' => 4],
            ['name' => 'Créativité', 'category' => 'transversale', 'cnp_code' => null, 'importance' => 3],
            
            // Compétences numériques
            ['name' => 'Maîtrise de Microsoft Office', 'category' => 'numerique', 'cnp_code' => null, 'importance' => 4],
            ['name' => 'Analyse de données', 'category' => 'numerique', 'cnp_code' => '2172', 'importance' => 4],
            ['name' => 'Réseaux sociaux', 'category' => 'numerique', 'cnp_code' => '1123', 'importance' => 3],
            ['name' => 'CAO/DAO', 'category' => 'numerique', 'cnp_code' => '2253', 'importance' => 4],
            ['name' => 'Base de données', 'category' => 'numerique', 'cnp_code' => '2172', 'importance' => 4],
            
            // Compétences linguistiques
            ['name' => 'Français avancé', 'category' => 'linguistique', 'cnp_code' => null, 'importance' => 4],
            ['name' => 'Anglais professionnel', 'category' => 'linguistique', 'cnp_code' => null, 'importance' => 4],
            ['name' => 'Rédaction technique', 'category' => 'linguistique', 'cnp_code' => null, 'importance' => 3],
            
            // Compétences de gestion
            ['name' => 'Gestion de projet', 'category' => 'gestion', 'cnp_code' => '0711', 'importance' => 4],
            ['name' => 'Budgétisation', 'category' => 'gestion', 'cnp_code' => '1111', 'importance' => 4],
            ['name' => 'Gestion des ressources humaines', 'category' => 'gestion', 'cnp_code' => '1121', 'importance' => 4],
            ['name' => 'Planification stratégique', 'category' => 'gestion', 'cnp_code' => '0111', 'importance' => 3],
            
            // Compétences de vente et service client
            ['name' => 'Vente', 'category' => 'commercial', 'cnp_code' => '6421', 'importance' => 4],
            ['name' => 'Service à la clientèle', 'category' => 'commercial', 'cnp_code' => '6552', 'importance' => 4],
            ['name' => 'Négociation', 'category' => 'commercial', 'cnp_code' => '6411', 'importance' => 4],
            ['name' => 'Marketing', 'category' => 'commercial', 'cnp_code' => '1123', 'importance' => 4],
        ];

        $bar = $this->output->createProgressBar(count($coreSkills));

        foreach ($coreSkills as $skillData) {
            Skill::updateOrCreate(
                ['slug' => Str::slug($skillData['name'])],
                [
                    'name' => $skillData['name'],
                    'category' => $skillData['category'],
                    'cnp_code' => $skillData['cnp_code'],
                    'importance_level' => $skillData['importance'],
                    'is_active' => true,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✓ ' . count($coreSkills) . ' compétences de base importées');
    }

    private function mapSkillsToSectors()
    {
        $this->info('🔗 Mapping des compétences aux secteurs...');

        $sectorSkillMappings = [
            // Construction (23)
            '23' => [
                'core_skills' => ['lecture-de-plans-et-devis', 'utilisation-d-outils-electriques', 'electricite'],
                'important_skills' => ['soudure', 'maconnerie', 'plomberie', 'travail-d-equipe', 'resolution-de-problemes'],
                'useful_skills' => ['gestion-de-projet', 'premiers-secours', 'communication-orale', 'cao-dao']
            ],
            
            // Soins de santé (62)
            '62' => [
                'core_skills' => ['soins-infirmiers', 'diagnostic-medical', 'premiers-secours'],
                'important_skills' => ['pharmacologie', 'radiologie', 'physiotherapie', 'communication-orale', 'travail-d-equipe'],
                'useful_skills' => ['francais-avance', 'anglais-professionnel', 'maitrise-de-microsoft-office', 'adaptabilite']
            ],
            
            // Services professionnels (54)
            '54' => [
                'core_skills' => ['droit-commercial', 'comptabilite', 'programmation', 'architecture', 'genie-civil'],
                'important_skills' => ['design-graphique', 'gestion-de-projet', 'communication-ecrite', 'analyse-de-donnees'],
                'useful_skills' => ['leadership', 'negociation', 'creativite', 'planification-strategique']
            ],
            
            // Fabrication (31-33)
            '31-33' => [
                'core_skills' => ['usinage', 'controle-qualite', 'maintenance-industrielle'],
                'important_skills' => ['automatisation', 'transformation-alimentaire', 'resolution-de-problemes', 'travail-d-equipe'],
                'useful_skills' => ['gestion-du-temps', 'premiers-secours', 'communication-orale', 'adaptabilite']
            ],
            
            // Commerce de détail (44-45)
            '44-45' => [
                'core_skills' => ['vente', 'service-a-la-clientele'],
                'important_skills' => ['communication-orale', 'travail-d-equipe', 'maitrise-de-microsoft-office', 'gestion-du-temps'],
                'useful_skills' => ['marketing', 'francais-avance', 'anglais-professionnel', 'adaptabilite']
            ],
            
            // Information et culture (51)
            '51' => [
                'core_skills' => ['programmation', 'design-graphique', 'redaction-technique'],
                'important_skills' => ['reseaux-sociaux', 'marketing', 'creativite', 'analyse-de-donnees'],
                'useful_skills' => ['gestion-de-projet', 'communication-ecrite', 'anglais-professionnel', 'base-de-donnees']
            ]
        ];

        $totalMappings = 0;
        foreach ($sectorSkillMappings as $sectorCode => $skillCategories) {
            $sector = Sector::where('scian_code', $sectorCode)->first();
            
            if (!$sector) {
                $this->warn("⚠️  Secteur {$sectorCode} non trouvé");
                continue;
            }

            // Compétences clés (score 5)
            foreach ($skillCategories['core_skills'] as $skillSlug) {
                $this->createSectorSkillMapping($sector, $skillSlug, 5, true);
                $totalMappings++;
            }

            // Compétences importantes (score 4)
            foreach ($skillCategories['important_skills'] as $skillSlug) {
                $this->createSectorSkillMapping($sector, $skillSlug, 4, false);
                $totalMappings++;
            }

            // Compétences utiles (score 3)
            foreach ($skillCategories['useful_skills'] as $skillSlug) {
                $this->createSectorSkillMapping($sector, $skillSlug, 3, false);
                $totalMappings++;
            }
        }

        $this->info("✓ {$totalMappings} associations secteur-compétences créées");
    }

    private function createSectorSkillMapping($sector, $skillSlug, $relevanceScore, $isCoreSkill)
    {
        $skill = Skill::where('slug', $skillSlug)->first();
        
        if (!$skill) {
            $this->warn("⚠️  Compétence '{$skillSlug}' non trouvée");
            return;
        }

        SectorSkill::updateOrCreate(
            [
                'sector_id' => $sector->id,
                'skill_id' => $skill->id
            ],
            [
                'relevance_score' => $relevanceScore,
                'is_core_skill' => $isCoreSkill
            ]
        );
    }
}