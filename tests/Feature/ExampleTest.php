<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302); // Karena di-redirect ke /dashboard atau /login
    }

    public function test_login_page_loads_with_star_admin_ui(): void
    {
        auth()->logout();

        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('BuildSync ERP');
        $response->assertSee('Construction &amp; Financial Management System', false);
        $response->assertSee('Work Email Address');
        $response->assertSee('Password');
        $response->assertSee('Remember me');
        $response->assertSee('Default: password');
        $response->assertSee('Sign In to Workspace');
        $response->assertSee('BuildSync ERP Enterprise');
        $response->assertSee('theme-toggle');

        // Test Indonesian guest locale
        $idResponse = $this->withSession(['locale' => 'id'])->get(route('login'));
        $idResponse->assertOk();
        $idResponse->assertSee('Sistem Manajemen Konstruksi &amp; Finansial', false);
        $idResponse->assertSee('Alamat Email Kerja');
        $idResponse->assertSee('Kata Sandi');
        $idResponse->assertSee('Ingat saya');
        $idResponse->assertSee('Masuk ke Workspace');
    }
}