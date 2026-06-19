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
        Schema::create('sector_skills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sector_id');
            $table->unsignedBigInteger('skill_id')->index();
            $table->integer('relevance_score')->default(3);
            $table->boolean('is_core_skill')->default(false);
            $table->timestamps();

            $table->index(['sector_id', 'is_core_skill']);
            $table->unique(['sector_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sector_skills');
    }
};
