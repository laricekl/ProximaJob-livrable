<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class SecuritySetting extends Model
{
    use HasFactory;

    protected $table = 'security_settings';

    protected $fillable = [
        'enable_2fa',
        'complex_passwords',
        'password_expiration',
        'password_expiration_days',
        'brute_force_protection',
        'admin_ips',
        'login_attempts_log',
        'admin_activities_log',
        'log_retention_days'
    ];

    protected $casts = [
        'enable_2fa' => 'boolean',
        'complex_passwords' => 'boolean',
        'password_expiration' => 'boolean',
        'brute_force_protection' => 'boolean',
        'login_attempts_log' => 'boolean',
        'admin_activities_log' => 'boolean',
        'admin_ips' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'enable_2fa' => false,
        'complex_passwords' => true,
        'password_expiration' => false,
        'brute_force_protection' => true,
        'login_attempts_log' => true,
        'admin_activities_log' => true,
        'log_retention_days' => 30
    ];

    public static function getDefaultSettings()
    {
        return [
            'enable_2fa' => false,
            'complex_passwords' => true,
            'password_expiration' => false,
            'password_expiration_days' => null,
            'brute_force_protection' => true,
            'admin_ips' => [],
            'login_attempts_log' => true,
            'admin_activities_log' => true,
            'log_retention_days' => 30
        ];
    }

    public static function validationRules()
    {
        return [
            'enable_2fa' => ['boolean'],
            'complex_passwords' => ['boolean'],
            'password_expiration' => ['boolean'],
            'password_expiration_days' => [
                'nullable',
                'integer',
                'min:1',
                'max:365',
                Rule::requiredIf(function () {
                    return request()->input('password_expiration') == true;
                })
            ],
            'brute_force_protection' => ['boolean'],
            'admin_ips' => ['nullable', 'json'],
            'login_attempts_log' => ['boolean'],
            'admin_activities_log' => ['boolean'],
            'log_retention_days' => ['integer', 'min:1', 'max:365']
        ];
    }

    public function getAdminIpsListAttribute()
    {
        return is_array($this->admin_ips) ? $this->admin_ips : [];
    }

    public function setAdminIpsAttribute($value)
    {
        $this->attributes['admin_ips'] = is_array($value) 
            ? json_encode($value) 
            : $value;
    }

    public function isIpAllowed($ip)
    {
        if (empty($this->admin_ips)) {
            return true;
        }

        return in_array($ip, $this->admin_ips_list);
    }
}