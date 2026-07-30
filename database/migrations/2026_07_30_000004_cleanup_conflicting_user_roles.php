<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $candidateRoleId = DB::table('roles')->where('name', 'candidat')->value('id');
        $privilegedRoleIds = DB::table('roles')
            ->whereIn('name', ['entreprise', 'admin', 'Marketing'])
            ->pluck('id');

        if (! $candidateRoleId || $privilegedRoleIds->isEmpty()) {
            return;
        }

        $conflictingUserIds = DB::table('model_has_roles')
            ->where('model_type', App\Models\User::class)
            ->whereIn('role_id', $privilegedRoleIds)
            ->pluck('model_id');

        DB::table('model_has_roles')
            ->where('model_type', App\Models\User::class)
            ->where('role_id', $candidateRoleId)
            ->whereIn('model_id', $conflictingUserIds)
            ->delete();
    }

    public function down(): void
    {
        // Les associations candidates erronées ne doivent pas être recréées.
    }
};
