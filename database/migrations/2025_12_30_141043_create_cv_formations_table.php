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
        Schema::create('cv_formations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cv_profile_id')->index();
            $table->string('periode', 100)->nullable();
            $table->string('diplome', 500);
            $table->integer('diplome_id')->nullable()->index();
            $table->string('etablissement', 500)->nullable();
            $table->integer('ordre')->nullable()->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_formations');
    }
};
