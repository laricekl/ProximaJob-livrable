<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diplomes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nom_diplome', 200);
            $table->string('nom_anglais', 200)->nullable();
            $table->string('sigle', 20)->nullable()->index();
            $table->enum('niveau_education', ['SECONDAIRE', 'COLLEGIAL', 'UNIVERSITAIRE_1ER_CYCLE', 'UNIVERSITAIRE_2E_CYCLE', 'UNIVERSITAIRE_3E_CYCLE', 'PROFESSIONNEL'])->index();
            $table->decimal('duree_annees', 2, 1)->nullable();
            $table->enum('statut', ['ACTIF', 'INACTIF'])->nullable()->default('ACTIF');
            $table->timestamp('date_creation')->useCurrent();
            $table->timestamp('date_modification')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diplomes');
    }
};
