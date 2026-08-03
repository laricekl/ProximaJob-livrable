<?php

namespace App\Http\Requests\Auth;

use App\Models\SecuritySetting;
use App\Models\User;
use App\Services\AdminAuditService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        if ($this->bruteForceProtectionEnabled()) {
            $this->ensureIsNotRateLimited();
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            if ($this->bruteForceProtectionEnabled()) {
                RateLimiter::hit($this->throttleKey());
            }

            $user = User::where('email', $this->string('email'))->first();
            if ($user?->hasAnyRole(['admin', 'Marketing'])) {
                app(AdminAuditService::class)->recordLogin(
                    'login.failed',
                    $user,
                    'Échec de connexion administrateur.',
                    ['reason' => 'invalid_credentials']
                );
            }

            $this->throwAuthFailedException();
        }

        RateLimiter::clear($this->throttleKey());
    }

    protected function throwAuthFailedException(): void
    {
        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => trans('auth.failed'),
                ], 401)
            );
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => trans('auth.throttle', [
                        'seconds' => $seconds,
                        'minutes' => ceil($seconds / 60),
                    ]),
                ], 429)
            );
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    private function bruteForceProtectionEnabled(): bool
    {
        if (! Schema::hasTable('security_settings')) {
            return true;
        }

        return (bool) SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings())->brute_force_protection;
    }
}
