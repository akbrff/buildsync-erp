<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_defaults_to_english(): void
    {
        auth()->logout();

        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());
        $response->assertSee('BuildSync ERP');
        $response->assertSee('Construction &amp; Financial Management System', false);
        $response->assertSee('Sign In to Workspace');
        $response->assertSee('English');
    }

    public function test_guest_can_switch_locale_via_route(): void
    {
        auth()->logout();

        $response = $this->get(route('locale.switch', ['locale' => 'id']));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'id');

        $pageResponse = $this->withSession(['locale' => 'id'])->get(route('login'));
        $pageResponse->assertStatus(200);
        $this->assertEquals('id', app()->getLocale());
        $pageResponse->assertSee('Sistem Manajemen Konstruksi &amp; Finansial', false);
        $pageResponse->assertSee('Masuk ke Workspace');
    }

    public function test_authenticated_user_defaults_to_english(): void
    {
        $user = User::factory()->create([
            'locale' => 'en',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());
        $response->assertSee('Total Projects');
        $response->assertSee('Project Progress');
        $response->assertSee('Budget Trend vs Realization');
    }

    public function test_authenticated_user_saved_locale_takes_priority_over_guest_session(): void
    {
        // User with Indonesian saved in DB
        $user = User::factory()->create([
            'locale' => 'id',
        ]);

        // Prior guest session had 'en'
        $response = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('dashboard'));

        $response->assertStatus(200);
        // DB preference 'id' must win over old session 'en'
        $this->assertEquals('id', app()->getLocale());
        $response->assertSee('Total Proyek');
        $response->assertSee('Progres Proyek');
        $response->assertSee('Tren Realisasi Anggaran vs Pagu');
    }

    public function test_authenticated_user_can_switch_locale_via_route_and_it_persists_in_database(): void
    {
        $user = User::factory()->create([
            'locale' => 'en',
        ]);

        $response = $this->actingAs($user)->get(route('locale.switch', ['locale' => 'id']));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'id');

        $user->refresh();
        $this->assertEquals('id', $user->locale);

        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);
        $this->assertEquals('id', app()->getLocale());
        $dashboardResponse->assertSee('Total Proyek');
        $dashboardResponse->assertSee('Progres Proyek');
    }

    public function test_authenticated_user_can_update_language_setting_via_settings_page(): void
    {
        $user = User::factory()->create([
            'locale' => 'en',
        ]);

        $response = $this->actingAs($user)->put(route('settings.language.update'), [
            'locale' => 'id',
        ]);

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('id', $user->locale);
        $this->assertEquals('id', session('locale'));
    }

    public function test_invalid_locale_falls_back_to_english(): void
    {
        $response = $this->get(route('locale.switch', ['locale' => 'unsupported_code']));

        $response->assertRedirect();
        $this->assertEquals('en', app()->getLocale());
        $this->assertEquals('en', session('locale'));
    }

    public function test_settings_page_displays_language_options_for_all_roles(): void
    {
        $roles = ['superadmin', 'project_manager', 'logistics', 'finance', 'field_staff'];

        foreach ($roles as $role) {
            $user = User::factory()->create([
                'role' => $role,
                'locale' => 'en',
            ]);

            $response = $this->actingAs($user)->get(route('settings.index'));

            $response->assertStatus(200);
            $response->assertSee('Language &amp; Regional Settings', false);
            $response->assertSee('English');
            $response->assertSee('Bahasa Indonesia');
            $response->assertSee('Save Settings');
        }
    }
}
