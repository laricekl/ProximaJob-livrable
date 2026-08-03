<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\SecuritySetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AdminAuditService
{
    public function record(
        string $action,
        ?Model $subject,
        string $description,
        array $metadata = [],
        ?int $adminId = null,
        bool $force = false,
    ): void {
        if (! Schema::hasTable('admin_audit_logs')) {
            return;
        }

        $settings = $this->settings();
        if (! $force && $settings && ! $settings->admin_activities_log) {
            return;
        }

        AdminAuditLog::create([
            'admin_id' => $adminId ?? auth()->id(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'metadata' => $metadata ?: null,
            'ip_address' => request()->ip(),
        ]);

        $this->prune($settings);
    }

    public function recordLogin(string $action, ?Model $user, string $description, array $metadata = []): void
    {
        $settings = $this->settings();
        if ($settings && ! $settings->login_attempts_log) {
            return;
        }

        $this->record($action, $user, $description, $metadata, $user?->getKey(), true);
    }

    private function settings(): ?SecuritySetting
    {
        if (! Schema::hasTable('security_settings')) {
            return null;
        }

        return SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings());
    }

    private function prune(?SecuritySetting $settings): void
    {
        $days = max(1, (int) ($settings?->log_retention_days ?? 30));
        AdminAuditLog::where('created_at', '<', now()->subDays($days))->delete();
    }
}
