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
        Schema::create('cv_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('nom', 255);
            $table->string('prenom', 255);
            $table->string('email', 255);
            $table->string('telephone', 50)->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville', 255)->nullable();
            $table->string('code_postal', 20)->nullable();
            $table->string('province', 100)->nullable();
            $table->text('langues_competences')->nullable();
            $table->text('logiciels')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_profiles');
    }
};
