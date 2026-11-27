<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Traduction automatique en cache des textes libres des offres.
     * Le français reste le texte source (canonique) ; les colonnes *_en
     * gardent la traduction générée une seule fois (voir OffreTranslationService).
     */
    public function up(): void
    {
        Schema::table('offres', function (Blueprint $table) {
            $table->string('titre_en', 255)->nullable();
            $table->string('poste_en', 255)->nullable();
            $table->longText('description_en')->nullable();
            $table->text('missions_en')->nullable();
            $table->text('responsibilities_en')->nullable();
            $table->text('objectif_en')->nullable();
            $table->text('criteres_en')->nullable();
            $table->string('traduction_hash', 64)->nullable();
            $table->timestamp('traduit_le')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('offres', function (Blueprint $table) {
            $table->dropColumn([
                'titre_en',
                'poste_en',
                'description_en',
                'missions_en',
                'responsibilities_en',
                'objectif_en',
                'criteres_en',
                'traduction_hash',
                'traduit_le',
            ]);
        });
    }
};
