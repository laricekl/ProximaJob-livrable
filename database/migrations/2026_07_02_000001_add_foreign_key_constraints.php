<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        // Nettoyer les orphelins avant d'ajouter les FK
        $this->cleanOrphans();

        // Ajouter les contraintes de clés étrangères
        // Note : les types sont déjà compatibles (integer avec integer, unsignedBigInteger avec bigIncrements)

        // === postulations ===
        Schema::table('postulations', function (Blueprint $table) {
            if (!$this->hasForeign('postulations', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
            if (!$this->hasForeign('postulations', 'offre_id')) {
                $table->foreign('offre_id')->references('id')->on('offres')->onDelete('cascade');
            }
        });

        // === offres ===
        Schema::table('offres', function (Blueprint $table) {
            if (!$this->hasForeign('offres', 'entreprise_id')) {
                $table->foreign('entreprise_id')->references('id')->on('entreprises')->onDelete('cascade');
            }
            if (!$this->hasForeign('offres', 'categorie_id')) {
                $table->foreign('categorie_id')->references('id')->on('categories')->onDelete('set null');
            }
            if (!$this->hasForeign('offres', 'sector_id')) {
                $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('set null');
            }
            if (!$this->hasForeign('offres', 'type_id')) {
                $table->foreign('type_id')->references('id')->on('types_offres')->onDelete('restrict');
            }
        });

        // === entreprises ===
        Schema::table('entreprises', function (Blueprint $table) {
            if (!$this->hasForeign('entreprises', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });

        // === candidate_sectors ===
        Schema::table('candidate_sectors', function (Blueprint $table) {
            if (!$this->hasForeign('candidate_sectors', 'candidate_id')) {
                $table->foreign('candidate_id')->references('id')->on('users')->onDelete('cascade');
            }
            if (!$this->hasForeign('candidate_sectors', 'sector_id')) {
                $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('set null');
            }
            if (!$this->hasForeign('candidate_sectors', 'diplome_id')) {
                $table->foreign('diplome_id')->references('id')->on('diplomes')->onDelete('set null');
            }
        });

        // === candidate_skills ===
        Schema::table('candidate_skills', function (Blueprint $table) {
            if (!$this->hasForeign('candidate_skills', 'candidate_id')) {
                $table->foreign('candidate_id')->references('id')->on('users')->onDelete('cascade');
            }
            if (!$this->hasForeign('candidate_skills', 'skill_id')) {
                $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            }
        });

        // === sector_skills ===
        Schema::table('sector_skills', function (Blueprint $table) {
            if (!$this->hasForeign('sector_skills', 'sector_id')) {
                $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('cascade');
            }
            if (!$this->hasForeign('sector_skills', 'skill_id')) {
                $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            }
        });

        // === job_offer_skills ===
        Schema::table('job_offer_skills', function (Blueprint $table) {
            if (!$this->hasForeign('job_offer_skills', 'job_offer_id')) {
                $table->foreign('job_offer_id')->references('id')->on('offres')->onDelete('cascade');
            }
            if (!$this->hasForeign('job_offer_skills', 'skill_id')) {
                $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            }
        });

        // === offre_diplome ===
        Schema::table('offre_diplome', function (Blueprint $table) {
            if (!$this->hasForeign('offre_diplome', 'offre_id')) {
                $table->foreign('offre_id')->references('id')->on('offres')->onDelete('cascade');
            }
            if (!$this->hasForeign('offre_diplome', 'diplome_id')) {
                $table->foreign('diplome_id')->references('id')->on('diplomes')->onDelete('cascade');
            }
        });

        // === user_abonnements ===
        Schema::table('user_abonnements', function (Blueprint $table) {
            if (!$this->hasForeign('user_abonnements', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
            if (!$this->hasForeign('user_abonnements', 'abonnement_id')) {
                $table->foreign('abonnement_id')->references('id')->on('abonnements')->onDelete('cascade');
            }
        });

        // === abonnement_fonctionnalites ===
        Schema::table('abonnement_fonctionnalites', function (Blueprint $table) {
            if (!$this->hasForeign('abonnement_fonctionnalites', 'abonnement_id')) {
                $table->foreign('abonnement_id')->references('id')->on('abonnements')->onDelete('cascade');
            }
        });

        // === cv_profiles ===
        Schema::table('cv_profiles', function (Blueprint $table) {
            if (!$this->hasForeign('cv_profiles', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });

        // === cv_formations ===
        Schema::table('cv_formations', function (Blueprint $table) {
            if (!$this->hasForeign('cv_formations', 'cv_profile_id')) {
                $table->foreign('cv_profile_id')->references('id')->on('cv_profiles')->onDelete('cascade');
            }
            if (!$this->hasForeign('cv_formations', 'diplome_id')) {
                $table->foreign('diplome_id')->references('id')->on('diplomes')->onDelete('set null');
            }
        });

        // === cv_competences, cv_experiences, cv_langues, cv_benevolats, cv_perfectionnements, cv_generes ===
        $cvRelationTables = ['cv_competences', 'cv_experiences', 'cv_langues', 'cv_benevolats', 'cv_perfectionnements', 'cv_generes'];
        foreach ($cvRelationTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!$this->hasForeign($tableName, 'cv_profile_id')) {
                    $table->foreign('cv_profile_id')->references('id')->on('cv_profiles')->onDelete('cascade');
                }
            });
        }

        // === notifications ===
        Schema::table('notifications', function (Blueprint $table) {
            if (!$this->hasForeign('notifications', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });

        // === autres_docs ===
        Schema::table('autres_docs', function (Blueprint $table) {
            if (!$this->hasForeign('autres_docs', 'id_postulation')) {
                $table->foreign('id_postulation')->references('id')->on('postulations')->onDelete('cascade');
            }
        });
    }

    private function hasForeign(string $table, string $column): bool
    {
        // SQLite : la liste des foreign keys n'est pas accessible via information_schema
        try {
            $foreignKeys = DB::select("PRAGMA foreign_key_list(`{$table}`)");
            foreach ($foreignKeys as $fk) {
                if ($fk->from === $column) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            // Si on ne peut pas vérifier, on tente la création (peut échouer si déjà existant)
        }
        return false;
    }

    private function cleanOrphans(): void
    {
        $cleaned = 0;

        try {
            $cleaned += DB::table('postulations')
                ->whereNotIn('user_id', DB::table('users')->pluck('id'))
                ->delete();
        } catch (\Exception $e) {}

        try {
            $cleaned += DB::table('postulations')
                ->whereNotIn('offre_id', DB::table('offres')->pluck('id'))
                ->delete();
        } catch (\Exception $e) {}

        if ($cleaned > 0) {
            Log::warning("Migration FK : {$cleaned} enregistrements orphelins nettoyés.");
        }
    }

    public function down(): void
    {
        // La suppression des FK est gérée par SQLite automatiquement quand on drop les tables
        // Pour MySQL, on les supprime explicitement
        if (DB::getDriverName() !== 'sqlite') {
            $foreignKeys = [
                ['postulations', ['user_id', 'offre_id']],
                ['offres', ['entreprise_id', 'categorie_id', 'sector_id', 'type_id']],
                ['entreprises', ['user_id']],
                ['candidate_sectors', ['candidate_id', 'sector_id', 'diplome_id']],
                ['candidate_skills', ['candidate_id', 'skill_id']],
                ['sector_skills', ['sector_id', 'skill_id']],
                ['job_offer_skills', ['job_offer_id', 'skill_id']],
                ['offre_diplome', ['offre_id', 'diplome_id']],
                ['user_abonnements', ['user_id', 'abonnement_id']],
                ['abonnement_fonctionnalites', ['abonnement_id']],
                ['cv_profiles', ['user_id']],
                ['cv_formations', ['cv_profile_id', 'diplome_id']],
                ['cv_competences', ['cv_profile_id']],
                ['cv_experiences', ['cv_profile_id']],
                ['cv_langues', ['cv_profile_id']],
                ['cv_benevolats', ['cv_profile_id']],
                ['cv_perfectionnements', ['cv_profile_id']],
                ['cv_generes', ['cv_profile_id']],
                ['notifications', ['user_id']],
                ['autres_docs', ['id_postulation']],
            ];

            foreach ($foreignKeys as [$table, $columns]) {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    foreach ($columns as $col) {
                        try {
                            $table->dropForeign([$col]);
                        } catch (\Exception $e) {}
                    }
                });
            }
        }
    }
};
