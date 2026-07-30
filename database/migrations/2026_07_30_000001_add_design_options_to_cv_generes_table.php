<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cv_generes', function (Blueprint $table) {
            $table->string('template_style', 30)->default('modern')->after('chemin_fichier');
            $table->string('accent_color', 30)->default('blue')->after('template_style');
            $table->string('font_style', 30)->default('sober')->after('accent_color');
            $table->string('density', 30)->default('balanced')->after('font_style');
            $table->string('section_order', 40)->default('skills_first')->after('density');
            $table->unsignedTinyInteger('page_limit')->default(2)->after('section_order');
            $table->string('summary_tone', 30)->default('professional')->after('page_limit');
            $table->json('visible_sections')->nullable()->after('summary_tone');
        });
    }

    public function down(): void
    {
        Schema::table('cv_generes', function (Blueprint $table) {
            $table->dropColumn([
                'template_style',
                'accent_color',
                'font_style',
                'density',
                'section_order',
                'page_limit',
                'summary_tone',
                'visible_sections',
            ]);
        });
    }
};
