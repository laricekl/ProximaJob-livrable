<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntreprisesTable extends Migration
{
    public function up()
    {
        Schema::table('entreprises', function (Blueprint $table) {
            // Informations légales obligatoires
            $table->string('rccm')->nullable()->comment('Numéro RCCM');
            $table->string('neq')->nullable()->comment('Identifiant Fiscal Unique');
            $table->string('cnss')->nullable()->comment('Numéro CNSS si employeur');
            $table->string('patente')->nullable()->comment('Numéro de patente');
            
            // Détails de l'entreprise
            $table->enum('forme_juridique', ['SARL', 'SA', 'SAS', 'Entreprise individuelle', 'GIE', 'SUARL', 'SNC', 'SCS'])
                  ->nullable()
                  ->comment('Forme juridique de l\'entreprise');
            $table->date('date_creation')->nullable()->comment('Date de création/immatriculation');
            $table->decimal('capital_social', 15, 2)->nullable()->comment('Capital social');
            $table->string('secteur_activite')->nullable()->comment('Secteur d\'activité principal');
            $table->text('adresse_siege')->nullable()->comment('Adresse du siège social');
            
            // Dirigeants
            $table->string('nom_dirigeant')->nullable()->comment('Nom du dirigeant/gérant');
            $table->string('fonction_dirigeant')->nullable()->comment('Fonction du dirigeant');
            
            // Documents justificatifs
            $table->string('document_rccm')->nullable()->comment('Chemin vers le document RCCM');
            $table->string('document_neq')->nullable()->comment('Chemin vers le document neq');
            $table->string('document_statuts')->nullable()->comment('Chemin vers les statuts');
            $table->string('document_attestation_fiscale')->nullable()->comment('Chemin vers l\'attestation fiscale');
            
            // Statut de vérification
            $table->enum('statut_verification', ['en_attente', 'verifie', 'rejete'])
                  ->default('en_attente')
                  ->comment('Statut de vérification de l\'entreprise');
            $table->text('commentaire_verification')->nullable()->comment('Commentaires de l\'admin lors de la vérification');
            $table->timestamp('date_verification')->nullable()->comment('Date de vérification');
            $table->unsignedBigInteger('verifie_par')->nullable()->comment('ID de l\'admin qui a vérifié');
            
            // Informations de contact professionnel
            $table->string('telephone_fixe')->nullable()->comment('Numéro de téléphone fixe');
            $table->string('email_professionnel')->nullable()->comment('Email professionnel');
            
            // Index pour optimiser les recherches
            $table->index('rccm');
            $table->index('neq');
            $table->index('statut_verification');
            
            // Clé étrangère
            $table->foreign('verifie_par')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropForeign(['verifie_par']);
            $table->dropIndex(['rccm']);
            $table->dropIndex(['neq']);
            $table->dropIndex(['statut_verification']);
            
            $table->dropColumn([
                'rccm', 'neq', 'cnss', 'patente',
                'forme_juridique', 'date_creation', 'capital_social', 'secteur_activite', 'adresse_siege',
                'nom_dirigeant', 'fonction_dirigeant',
                'document_rccm', 'document_neq', 'document_statuts', 'document_attestation_fiscale',
                'statut_verification', 'commentaire_verification', 'date_verification', 'verifie_par',
                'telephone_fixe', 'email_professionnel'
            ]);
        });
    }
}