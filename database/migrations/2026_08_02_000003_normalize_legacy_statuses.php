<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('entreprises')) {
            DB::table('entreprises')
                ->where('status', 'email_verification_pending')
                ->update(['status' => 'pending']);
        }

        if (Schema::hasTable('postulations')) {
            DB::table('postulations')->where('status', 'acceptee')->update(['status' => 'accepted']);
            DB::table('postulations')->where('status', 'refusee')->update(['status' => 'rejected']);
        }
    }

    public function down(): void
    {
        // Canonical statuses cannot safely be distinguished from historical rows.
    }
};
