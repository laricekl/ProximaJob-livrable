<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('offres', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('entreprises', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('postulations', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('abonnements', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('offres', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('postulations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('abonnements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
