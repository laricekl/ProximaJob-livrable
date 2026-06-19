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
        Schema::create('postulations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('offre_id')->index();
            $table->string('cv', 255)->nullable();
            $table->string('cover_letter', 255)->nullable();
            $table->boolean('autopostulation')->nullable()->default(false);
            $table->string('lettre_motivation', 255)->nullable();
            $table->string('status', 50)->nullable()->default('en_attente');
            $table->integer('match_score')->nullable();
            $table->date('application_date')->nullable();
            $table->string('algorithm_version', 10)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['user_id', 'offre_id'], 'uc_postulations_user_offre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulations');
    }
};
