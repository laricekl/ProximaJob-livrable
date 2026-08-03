<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    public function test_production_check_detects_an_unsafe_local_configuration(): void
    {
        $this->artisan('production:check')
            ->expectsOutputToContain('Production non prête')
            ->assertFailed();
    }
}
