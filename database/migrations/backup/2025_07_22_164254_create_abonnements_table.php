<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->integer('duree')->comment('Durée en jours');
            $table->unsignedBigInteger('tarif_id');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('tarif_id')->references('id')->on('tarifs');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abonnements');
    }
};