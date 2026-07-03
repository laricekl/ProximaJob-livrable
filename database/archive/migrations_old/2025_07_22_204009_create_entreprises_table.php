<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('company_name', 255);
            $table->text('description')->nullable();
            $table->string('website', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('entreprises');
    }
};