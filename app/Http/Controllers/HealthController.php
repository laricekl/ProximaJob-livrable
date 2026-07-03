<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Vérifie l'état de santé de l'application.
     */
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
        ];

        $allPassed = !in_array(false, array_column($checks, 'ok'), true);
        $status = $allPassed ? 200 : 503;

        return response()->json([
            'status' => $allPassed ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ], $status);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['ok' => true, 'message' => 'Connecté'];
        } catch (\Exception $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health_check_' . time();
            Cache::put($key, true, 10);
            $value = Cache::get($key);
            Cache::forget($key);
            return ['ok' => $value === true, 'message' => $value === true ? 'Fonctionnel' : 'Échec lecture/écriture'];
        } catch (\Exception $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }
}
