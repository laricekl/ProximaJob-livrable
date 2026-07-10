<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_campagnes', function (Blueprint $table) {
            $table->id();
            $table->string('sujet');
            $table->text('contenu');
            $table->string('audience')->default('tous'); // tous, candidats, entreprises, premium
            $table->string('statut')->default('brouillon'); // brouillon, envoyee, programmee
            $table->timestamp('envoyee_le')->nullable();
            $table->timestamp('programmee_pour')->nullable();
            $table->integer('destinataires_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_campagnes');
    }
};
