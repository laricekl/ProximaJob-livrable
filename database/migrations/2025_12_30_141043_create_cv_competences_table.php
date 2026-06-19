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
        Schema::create('cv_competences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cv_profile_id')->index();
            $table->text('description');
            $table->enum('type', ['specifique', 'generale'])->nullable()->default('specifique');
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
        Schema::dropIfExists('cv_competences');
    }
};
