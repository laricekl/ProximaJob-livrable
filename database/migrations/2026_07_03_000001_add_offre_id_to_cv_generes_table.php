<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_generes', function (Blueprint $table) {
            if (!Schema::hasColumn('cv_generes', 'offre_id')) {
                $table->unsignedBigInteger('offre_id')->nullable()->after('cv_profile_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cv_generes', function (Blueprint $table) {
            if (Schema::hasColumn('cv_generes', 'offre_id')) {
                $table->dropColumn('offre_id');
            }
        });
    }
};
