<?php

use App\Services\JobMatchingService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('postulations')) {
            return;
        }

        app(JobMatchingService::class)->recalculateStoredApplications();
    }

    public function down(): void
    {
        // Les anciens scores ne peuvent pas être restaurés de manière fiable.
    }
};
