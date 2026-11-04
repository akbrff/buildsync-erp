<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_loads(): void
    {
        $this->withoutVite();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('BuildSync ERP');
        $response->assertSee('Total Proyek');
        $response->assertSee('Progres Proyek');	
    }
}