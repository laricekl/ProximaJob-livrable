<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('postulations', 'admin_notes')) {
            Schema::table('postulations', function (Blueprint $table) {
                $table->text('admin_notes')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('postulations', 'admin_notes')) {
            Schema::table('postulations', fn (Blueprint $table) => $table->dropColumn('admin_notes'));
        }
    }
};
