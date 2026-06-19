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
        Schema::create('abonnement_fonctionnalites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('abonnement_id')->index();
            $table->string('nom', 255);
            $table->string('icone', 255)->nullable();
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnement_fonctionnalites');
    }
};
