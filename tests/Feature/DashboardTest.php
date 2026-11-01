<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardTest extends TestCase
{
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