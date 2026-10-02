<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_when_accessing_member_dashboard(): void
    {
        $response = $this->get('/member/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
        $response->assertSee($admin->name);
    }

    public function test_member_gets_403_forbidden_on_admin_routes(): void
    {
        $member = User::factory()->member()->create(['is_active' => true]);

        $response = $this->actingAs($member)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_member_can_access_member_dashboard(): void
    {
        $member = User::factory()->member()->create(['is_active' => true]);

        $response = $this->actingAs($member)->get('/member/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Member Dashboard');
        $response->assertSee($member->name);
    }

    public function test_admin_gets_403_forbidden_on_member_only_routes(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->get('/member/dashboard');

        $response->assertStatus(403);
    }

    public function test_user_deactivated_mid_session_is_logged_out_and_redirected(): void
    {
        $user = User::factory()->member()->create(['is_active' => true]);

        // User starts active session
        $this->actingAs($user);
        $this->get('/member/dashboard')->assertStatus(200);

        // Deactivate user in database
        $user->update(['is_active' => false]);

        // Next request triggers EnsureUserIsActive middleware
        $response = $this->get('/member/dashboard');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'Your account has been deactivated.',
        ]);
    }
}
