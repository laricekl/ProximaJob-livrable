<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sector;
use Illuminate\Support\Str;

class ImportScianSectors extends Command
{
    protected $signature = 'scian:import {--force : Force l\'import même si des données existent}';
    protected $description = 'Importe les secteurs SCIAN officiels du Canada';

    public function handle()
    {
        $this->info('🇨🇦 Import des secteurs SCIAN Canada...');

        // Vérifier si des données existent déjà
        if (Sector::count() > 0 && !$this->option('force')) {
            if (!$this->confirm('Des secteurs existent déjà. Continuer l\'import ?')) {
                $this->info('Import annulé.');
                return;
            }
        }

        try {
            // Ajouter la colonne scian_code si elle n'existe pas
            $this->addScianCodeColumn();
            
            // Import des secteurs principaux
            $this->importMainSectors();
            
            // Import des sous-secteurs
            $this->importSubSectors();
            
            $this->info('✅ Import SCIAN terminé avec succès !');
            $this->table(['Total secteurs'], [[Sector::count()]]);
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'import : ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function addScianCodeColumn()
    {
        // Cette partie nécessite toujours DB:: car c'est une opération de schéma
        // Mais on peut la rendre optionnelle si la colonne existe déjà
        if (!\Schema::hasColumn('sectors', 'scian_code')) {
            \Schema::table('sectors', function ($table) {
                $table->string('scian_code', 10)->nullable();
            });
            $this->info('✓ Colonne scian_code ajoutée');
        }
    }

    private function importMainSectors()
    {
        $this->info('📊 Import des secteurs principaux...');
        
        $mainSectors = [
            ['code' => '11', 'name' => 'Agriculture, foresterie, pêche et chasse'],
            ['code' => '21', 'name' => 'Extraction minière, exploitation en carrière, et extraction de pétrole et de gaz'],
            ['code' => '22', 'name' => 'Services publics'],
            ['code' => '23', 'name' => 'Construction'],
            ['code' => '31-33', 'name' => 'Fabrication'],
            ['code' => '41', 'name' => 'Commerce de gros'],
            ['code' => '44-45', 'name' => 'Commerce de détail'],
            ['code' => '48-49', 'name' => 'Transport et entreposage'],
            ['code' => '51', 'name' => 'Industrie de l\'information et industrie culturelle'],
            ['code' => '52', 'name' => 'Finance et assurances'],
            ['code' => '53', 'name' => 'Services immobiliers et services de location'],
            ['code' => '54', 'name' => 'Services professionnels, scientifiques et techniques'],
            ['code' => '55', 'name' => 'Gestion de sociétés et d\'entreprises'],
            ['code' => '56', 'name' => 'Services administratifs et services de soutien'],
            ['code' => '61', 'name' => 'Services d\'enseignement'],
            ['code' => '62', 'name' => 'Soins de santé et assistance sociale'],
            ['code' => '71', 'name' => 'Arts, spectacles et loisirs'],
            ['code' => '72', 'name' => 'Services d\'hébergement et de restauration'],
            ['code' => '81', 'name' => 'Autres services (sauf les administrations publiques)'],
            ['code' => '91', 'name' => 'Administrations publiques']
        ];

        $bar = $this->output->createProgressBar(count($mainSectors));

        foreach ($mainSectors as $sector) {
            Sector::updateOrCreate(
                ['scian_code' => $sector['code']],
                [
                    'name' => $sector['name'],
                    'slug' => Str::slug($sector['name']),
                    'parent_id' => null,
                    'is_active' => true,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✓ ' . count($mainSectors) . ' secteurs principaux importés');
    }

    private function importSubSectors()
    {
        $this->info('📋 Import des sous-secteurs...');

        $subSectors = [
            // Construction (23)
            ['parent_code' => '23', 'code' => '236', 'name' => 'Construction de bâtiments'],
            ['parent_code' => '23', 'code' => '237', 'name' => 'Construction d\'ouvrages de génie civil'],
            ['parent_code' => '23', 'code' => '238', 'name' => 'Entrepreneurs spécialisés'],
            ['parent_code' => '23', 'code' => '2361', 'name' => 'Construction résidentielle'],
            ['parent_code' => '23', 'code' => '2362', 'name' => 'Construction non résidentielle'],

            // Soins de santé (62)
            ['parent_code' => '62', 'code' => '621', 'name' => 'Soins de santé ambulatoires'],
            ['parent_code' => '62', 'code' => '622', 'name' => 'Hôpitaux'],
            ['parent_code' => '62', 'code' => '623', 'name' => 'Établissements de soins infirmiers'],
            ['parent_code' => '62', 'code' => '624', 'name' => 'Assistance sociale'],
            ['parent_code' => '62', 'code' => '6211', 'name' => 'Cabinets de médecins'],
            ['parent_code' => '62', 'code' => '6212', 'name' => 'Cabinets de dentistes'],

            // Services professionnels (54)
            ['parent_code' => '54', 'code' => '5411', 'name' => 'Services juridiques'],
            ['parent_code' => '54', 'code' => '5412', 'name' => 'Services de comptabilité'],
            ['parent_code' => '54', 'code' => '5413', 'name' => 'Services d\'architecture et de génie'],
            ['parent_code' => '54', 'code' => '5414', 'name' => 'Services de design spécialisés'],
            ['parent_code' => '54', 'code' => '5415', 'name' => 'Services de conseils en informatique'],

            // Fabrication (31-33)
            ['parent_code' => '31-33', 'code' => '311', 'name' => 'Fabrication d\'aliments'],
            ['parent_code' => '31-33', 'code' => '321', 'name' => 'Fabrication de produits en bois'],
            ['parent_code' => '31-33', 'code' => '325', 'name' => 'Fabrication de produits chimiques'],
            ['parent_code' => '31-33', 'code' => '336', 'name' => 'Fabrication de matériel de transport'],

            // Commerce de détail (44-45)
            ['parent_code' => '44-45', 'code' => '441', 'name' => 'Concessionnaires de véhicules automobiles'],
            ['parent_code' => '44-45', 'code' => '445', 'name' => 'Magasins d\'alimentation'],
            ['parent_code' => '44-45', 'code' => '448', 'name' => 'Magasins de vêtements et d\'accessoires vestimentaires'],

            // Information et culture (51)
            ['parent_code' => '51', 'code' => '511', 'name' => 'Édition'],
            ['parent_code' => '51', 'code' => '515', 'name' => 'Radiodiffusion et télévision'],
            ['parent_code' => '51', 'code' => '517', 'name' => 'Télécommunications'],
            ['parent_code' => '51', 'code' => '518', 'name' => 'Fournisseurs de services Internet'],
        ];

        $bar = $this->output->createProgressBar(count($subSectors));

        foreach ($subSectors as $sector) {
            $parent = Sector::where('scian_code', $sector['parent_code'])->first();

            if ($parent) {
                Sector::updateOrCreate(
                    ['scian_code' => $sector['code']],
                    [
                        'name' => $sector['name'],
                        'slug' => Str::slug($sector['name']),
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ]
                );
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✓ ' . count($subSectors) . ' sous-secteurs importés');
    }
}