<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_renders_new_buildsync_logo_and_retains_branding_text(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();

        // 1. New logo image is rendered with appropriate classes
        $response->assertSee('images/buildsync-logo-white.png', false);
        $response->assertSee('sidebar-brand-logo', false);
        $response->assertSee('mix-blend-screen', false);
        $response->assertSee('object-contain', false);

        // 2. Branding text is preserved
        $response->assertSee('BuildSync ERP');
        $response->assertSee('Enterprise Suite');

        // 3. Old building svg container in sidebar header is replaced
        $response->assertDontSee('M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', false);

        // 4. Favicon links exist in head
        $response->assertSee('favicon.ico', false);
        $response->assertSee('favicon.png', false);
    }

    public function test_login_page_renders_new_buildsync_logo_and_retains_branding_text(): void
    {
        auth()->logout();

        $response = $this->get(route('login'));

        $response->assertOk();

        // 1. New logo image is rendered using the exact same asset
        $response->assertSee('images/buildsync-logo-white.png', false);
        $response->assertSee('login-brand-logo', false);
        $response->assertSee('mix-blend-screen', false);
        $response->assertSee('object-contain', false);

        // 2. Branding text is preserved
        $response->assertSee('BuildSync ERP');

        // 3. Old building svg container is completely removed
        $response->assertDontSee('M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', false);

        // 4. Favicon links exist in head
        $response->assertSee('favicon.ico', false);
        $response->assertSee('favicon.png', false);
    }

    public function test_compiled_css_includes_sidebar_and_login_brand_logo_styles(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $this->assertFileExists($manifestPath);

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $cssFile = public_path('build/' . $manifest['resources/css/app.css']['file']);
        $this->assertFileExists($cssFile);

        $cssContent = file_get_contents($cssFile);
        $this->assertStringContainsString('.sidebar-brand-logo', $cssContent);
        $this->assertStringContainsString('.login-brand-logo', $cssContent);
        $this->assertStringContainsString('mix-blend-mode:screen', str_replace(' ', '', $cssContent));
    }
}
