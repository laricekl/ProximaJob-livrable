<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offres', function (Blueprint $table) {
            $table->index('poste');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('postulations', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('offres', function (Blueprint $table) {
            $table->dropIndex(['poste']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('postulations', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
