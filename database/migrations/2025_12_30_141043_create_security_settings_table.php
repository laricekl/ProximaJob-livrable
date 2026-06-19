<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('security_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('enable_2fa')->default(false);
            $table->boolean('complex_passwords')->default(true);
            $table->boolean('password_expiration')->default(false);
            $table->integer('password_expiration_days')->nullable();
            $table->boolean('brute_force_protection')->default(true);
            $table->text('admin_ips')->nullable()->comment('JSON array of allowed IPs');
            $table->boolean('login_attempts_log')->default(true);
            $table->boolean('admin_activities_log')->default(true);
            $table->integer('log_retention_days')->default(30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};
