<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('offres')) {
            return;
        }

        Schema::table('offres', function (Blueprint $table) {
            if (! Schema::hasColumn('offres', 'status')) {
                $table->string('status', 50)->default('active')->after('type_id');
            }

            if (! Schema::hasColumn('offres', 'slug')) {
                $table->string('slug')->nullable()->after('status');
            }

            if (! Schema::hasColumn('offres', 'salaire_min')) {
                $table->decimal('salaire_min', 10, 2)->nullable()->after('experience');
            }

            if (! Schema::hasColumn('offres', 'salaire_max')) {
                $table->decimal('salaire_max', 10, 2)->nullable()->after('salaire_min');
            }

            if (! Schema::hasColumn('offres', 'employment_type')) {
                $table->string('employment_type')->nullable()->after('date_fin');
            }

            if (! Schema::hasColumn('offres', 'remote_work')) {
                $table->string('remote_work')->nullable()->after('employment_type');
            }

            if (! Schema::hasColumn('offres', 'job_category')) {
                $table->string('job_category')->nullable()->after('remote_work');
            }

            if (! Schema::hasColumn('offres', 'salary_type')) {
                $table->string('salary_type')->nullable()->after('job_category');
            }

            if (! Schema::hasColumn('offres', 'start_date')) {
                $table->string('start_date')->nullable()->after('salary_type');
            }

            if (! Schema::hasColumn('offres', 'required_experience')) {
                $table->string('required_experience')->nullable()->after('start_date');
            }

            if (! Schema::hasColumn('offres', 'education_level')) {
                $table->string('education_level')->nullable()->after('required_experience');
            }

            if (! Schema::hasColumn('offres', 'responsibilities')) {
                $table->text('responsibilities')->nullable()->after('education_level');
            }
        });
    }

    public function down(): void
    {
        // Runtime compatibility migration: keep existing production data intact.
    }
};
