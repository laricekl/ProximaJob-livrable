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
        Schema::create('offres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('entreprise_id')->index();
            $table->string('titre', 255);
            $table->string('poste', 255);
            $table->longText('description')->nullable();
            $table->string('localisation', 255)->nullable();
            $table->string('experience', 255)->nullable();
            $table->decimal('salaire_min', 10)->nullable();
            $table->decimal('salaire_max', 10)->nullable();
            $table->unsignedBigInteger('categorie_id')->nullable()->default(1)->index();
            $table->integer('sector_id')->nullable()->index();
            $table->unsignedBigInteger('type_id')->index();
            $table->string('employment_type', 255)->nullable();
            $table->string('remote_work', 255)->nullable();
            $table->string('job_category', 255)->nullable();
            $table->string('salary_type', 255)->nullable();
            $table->string('status', 50)->default('active');
            $table->string('slug', 255)->nullable();
            $table->date('date_fin')->nullable();
            $table->string('start_date', 255)->nullable();
            $table->string('langues', 255)->nullable();
            $table->text('annee_experience')->nullable();
            $table->string('required_experience', 255)->nullable();
            $table->string('education_level', 255)->nullable();
            $table->text('criteres')->nullable();
            $table->text('missions')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('objectif')->nullable();
            $table->text('avantages')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};
