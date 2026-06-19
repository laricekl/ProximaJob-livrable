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
        Schema::create('autres_docs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('id_postulation')->index();
            $table->string('intitule', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('path', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autres_docs');
    }
};
