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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('prenom', 20)->nullable();
            $table->string('email', 255)->unique();
            $table->string('telephone', 20)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('status', 255)->nullable()->default('Actif');
            $table->string('cv', 255)->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->decimal('salary_expectation_min', 10, 0)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->rememberToken();
            $table->unsignedBigInteger('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
            $table->string('last_login_at', 255)->nullable();
            $table->string('provider', 255)->nullable();
            $table->string('provider_id', 255)->nullable();
            $table->string('avatar', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
