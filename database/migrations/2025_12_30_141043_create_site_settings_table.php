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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site_nom', 255);
            $table->string('email', 255);
            $table->string('tel', 255)->nullable();
            $table->string('localisation', 255)->nullable();
            $table->string('timezone', 255)->default('UTC');
            $table->string('logo', 255)->nullable();
            $table->string('favicon', 255)->nullable();
            $table->timestamps();
            $table->text('map_embed_url')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('map_zoom')->nullable()->default(15);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
