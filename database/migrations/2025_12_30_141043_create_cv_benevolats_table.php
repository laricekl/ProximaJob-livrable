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
        Schema::create('cv_benevolats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cv_profile_id')->index();
            $table->string('periode', 100)->nullable();
            $table->string('role', 500);
            $table->string('organisation', 500)->nullable();
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
        Schema::dropIfExists('cv_benevolats');
    }
};
