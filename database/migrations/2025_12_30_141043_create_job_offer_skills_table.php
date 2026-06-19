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
        Schema::create('job_offer_skills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_offer_id')->nullable()->index();
            $table->unsignedBigInteger('skill_id')->index();
            $table->string('skill_type', 255)->nullable()->comment('technical, methodological, digital');
            $table->boolean('is_required')->default(false)->index();
            $table->integer('weight')->default(5);
            $table->timestamps();

            $table->unique(['job_offer_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_offer_skills');
    }
};
