<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postulations', function (Blueprint $table) {
            $table->json('match_details')->nullable()->after('match_score');
            $table->text('lettre_motivation')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('postulations', function (Blueprint $table) {
            $table->dropColumn('match_details');
        });
    }
};
