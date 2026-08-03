<?php

namespace App\Support;

use App\Models\SecuritySetting;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class AdminPasswordPolicy
{
    public static function rule(?User $user = null, ?string $role = null): Password
    {
        $isAdministrative = in_array($role, ['admin', 'Marketing'], true)
            || $user?->hasAnyRole(['admin', 'Marketing']);

        if ($isAdministrative && self::complexPasswordsEnabled()) {
            return Password::min(12)->letters()->mixedCase()->numbers()->symbols();
        }

        return Password::min(8);
    }

    private static function complexPasswordsEnabled(): bool
    {
        if (! Schema::hasTable('security_settings')) {
            return true;
        }

        return (bool) SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings())->complex_passwords;
    }
}
