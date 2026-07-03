<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postulations', function (Blueprint $table) {
            $table->text('cover_letter')->nullable()->change();
            $table->text('lettre_motivation')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('postulations', function (Blueprint $table) {
            $table->string('cover_letter', 255)->nullable()->change();
            $table->string('lettre_motivation', 255)->nullable()->change();
        });
    }
};
