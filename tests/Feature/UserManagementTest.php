<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::first();
        if ($this->superadmin) {
            $this->superadmin->update([
                'name' => 'Administrator',
                'email' => 'admin@buildsync.com',
                'role' => 'superadmin',
                'status' => 'active',
            ]);
        } else {
            $this->superadmin = User::factory()->create([
                'name' => 'Administrator',
                'email' => 'admin@buildsync.com',
                'role' => 'superadmin',
                'status' => 'active',
            ]);
        }

        $this->actingAs($this->superadmin);
    }

    public function test_only_administrator_can_access_user_management(): void
    {
        // Superadmin has access (English default)
        $response = $this->actingAs($this->superadmin)->get(route('users.roles.index'));
        $response->assertOk();
        $response->assertSee('User Permissions');
        $response->assertSee('Total Users');
        $response->assertSee('Add User');

        // Indonesian translation check
        $this->superadmin->update(['locale' => 'id']);
        $idResponse = $this->actingAs($this->superadmin)->get(route('users.roles.index'));
        $idResponse->assertOk();
        $idResponse->assertSee('Hak Akses Pengguna');
        $idResponse->assertSee('Total Pengguna');
        $idResponse->assertSee('Tambah Pengguna');
        $this->superadmin->update(['locale' => 'en']);

        // Other roles are forbidden
        $roles = ['project_manager', 'finance', 'logistics', 'field_staff'];
        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role, 'status' => 'active']);
            $res = $this->actingAs($user)->get(route('users.roles.index'));
            $res->assertForbidden();
        }

        // Guests redirected to login
        auth()->logout();
        $this->get(route('users.roles.index'))->assertRedirect(route('login'));
    }

    public function test_administrator_can_filter_users(): void
    {
        $pm = User::factory()->create([
            'name' => 'John Project',
            'email' => 'john@buildsync.com',
            'role' => 'project_manager',
            'status' => 'active',
        ]);

        $inactiveUser = User::factory()->create([
            'name' => 'Jane Inactive',
            'email' => 'jane@buildsync.com',
            'role' => 'finance',
            'status' => 'inactive',
        ]);

        // Search by keyword
        $res = $this->actingAs($this->superadmin)->get(route('users.roles.index', ['q' => 'John']));
        $res->assertOk();
        $res->assertSee('John Project');
        $res->assertDontSee('Jane Inactive');

        // Filter by role
        $resRole = $this->actingAs($this->superadmin)->get(route('users.roles.index', ['role' => 'finance']));
        $resRole->assertOk();
        $resRole->assertSee('Jane Inactive');
        $resRole->assertDontSee('John Project');

        // Filter by status
        $resStatus = $this->actingAs($this->superadmin)->get(route('users.roles.index', ['status' => 'inactive']));
        $resStatus->assertOk();
        $resStatus->assertSee('Jane Inactive');
        $resStatus->assertDontSee('John Project');
    }

    public function test_administrator_can_create_new_user(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('users.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@buildsync.com',
            'role' => 'logistics',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@buildsync.com',
            'role' => 'logistics',
            'status' => 'active',
        ]);

        $created = User::where('email', 'budi@buildsync.com')->first();
        $this->assertTrue(Hash::check('password123', $created->password));
    }

    public function test_create_user_requires_valid_data_and_password_confirmation(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('users.store'), [
            'name' => '',
            'email' => 'invalid-email',
            'role' => 'unknown_role',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    public function test_administrator_can_edit_user_info(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@buildsync.com',
            'role' => 'finance',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superadmin)->put(route('users.update', $user), [
            'name' => 'New Name',
            'email' => 'new@buildsync.com',
            'role' => 'project_manager',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('new@buildsync.com', $user->email);
        $this->assertEquals('project_manager', $user->role);
    }

    public function test_administrator_can_reset_user_password(): void
    {
        $user = User::factory()->create([
            'role' => 'finance',
            'password' => Hash::make('old_secret_123'),
        ]);

        $response = $this->actingAs($this->superadmin)->patch(route('users.reset-password', $user), [
            'password' => 'new_secret_456',
            'password_confirmation' => 'new_secret_456',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('new_secret_456', $user->password));
        $this->assertFalse(Hash::check('old_secret_123', $user->password));
    }

    public function test_administrator_can_activate_and_deactivate_user(): void
    {
        $user = User::factory()->create([
            'role' => 'finance',
            'status' => 'active',
        ]);

        // Deactivate
        $res = $this->actingAs($this->superadmin)->patch(route('users.status.update', $user), [
            'status' => 'inactive',
        ]);
        $res->assertRedirect();
        $this->assertEquals('inactive', $user->fresh()->status);

        // Reactivate
        $res2 = $this->actingAs($this->superadmin)->patch(route('users.status.update', $user), [
            'status' => 'active',
        ]);
        $res2->assertRedirect();
        $this->assertEquals('active', $user->fresh()->status);
    }

    public function test_deactivated_user_cannot_login(): void
    {
        auth()->logout();

        $user = User::factory()->create([
            'email' => 'blocked@buildsync.com',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'blocked@buildsync.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_deactivation_invalidates_existing_sessions(): void
    {
        $user = User::factory()->create([
            'role' => 'finance',
            'status' => 'active',
        ]);

        // Simulate active session in DB
        DB::table('sessions')->insert([
            'id' => 'test_session_token_123',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'TestBrowser',
            'payload' => 'dummy_payload',
            'last_activity' => time(),
        ]);

        $this->assertEquals(1, DB::table('sessions')->where('user_id', $user->id)->count());

        // Deactivate user
        $this->actingAs($this->superadmin)->patch(route('users.status.update', $user), [
            'status' => 'inactive',
        ]);

        // Session record must be destroyed
        $this->assertEquals(0, DB::table('sessions')->where('user_id', $user->id)->count());

        // An active request by deactivated user is blocked
        $user->refresh();
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_protection_cannot_deactivate_or_delete_primary_administrator(): void
    {
        // Attempt to deactivate primary admin
        $resDeact = $this->actingAs($this->superadmin)->patch(route('users.status.update', $this->superadmin), [
            'status' => 'inactive',
        ]);
        $resDeact->assertSessionHas('error');
        $this->assertEquals('active', $this->superadmin->fresh()->status);

        // Attempt to delete primary admin
        $resDel = $this->actingAs($this->superadmin)->delete(route('users.destroy', $this->superadmin));
        $resDel->assertSessionHas('error');
        $this->assertNotSoftDeleted($this->superadmin);

        // Attempt to demote primary admin role
        $resRole = $this->actingAs($this->superadmin)->put(route('users.update', $this->superadmin), [
            'name' => 'Administrator',
            'email' => 'admin@buildsync.com',
            'role' => 'finance',
            'status' => 'active',
        ]);
        $resRole->assertSessionHas('error');
        $this->assertEquals('superadmin', $this->superadmin->fresh()->role);
    }

    public function test_protection_administrator_cannot_deactivate_or_delete_own_account(): void
    {
        // Create secondary superadmin
        $secondAdmin = User::factory()->create([
            'name' => 'Second Admin',
            'email' => 'second@buildsync.com',
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        // Second admin tries to deactivate self
        $resDeact = $this->actingAs($secondAdmin)->patch(route('users.status.update', $secondAdmin), [
            'status' => 'inactive',
        ]);
        $resDeact->assertSessionHas('error');
        $this->assertEquals('active', $secondAdmin->fresh()->status);

        // Second admin tries to delete self
        $resDel = $this->actingAs($secondAdmin)->delete(route('users.destroy', $secondAdmin));
        $resDel->assertSessionHas('error');
        $this->assertNotSoftDeleted($secondAdmin);
    }

    public function test_protection_cannot_deactivate_or_delete_last_active_superadmin(): void
    {
        // Single superadmin exists ($this->superadmin).
        // Try to deactivate via status endpoint
        $res = $this->actingAs($this->superadmin)->patch(route('users.status.update', $this->superadmin), [
            'status' => 'inactive',
        ]);
        $res->assertSessionHas('error');
        $this->assertEquals('active', $this->superadmin->fresh()->status);
    }

    public function test_administrator_can_soft_delete_other_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Temporary User',
            'email' => 'temp@buildsync.com',
            'role' => 'finance',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superadmin)->delete(route('users.destroy', $user));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
            'email' => 'temp@buildsync.com',
        ]);
    }
}
