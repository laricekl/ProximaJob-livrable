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
        Schema::create('offre_diplome', function (Blueprint $table) {
            $table->unsignedBigInteger('offre_id');
            $table->integer('diplome_id')->index();
            $table->boolean('obligatoire')->default(false);
            $table->timestamps();

            $table->primary(['offre_id', 'diplome_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offre_diplome');
    }
};
