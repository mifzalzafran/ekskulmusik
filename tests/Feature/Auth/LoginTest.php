<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Log In');
    }

    public function test_admin_can_authenticate_and_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin_test@example.com',
            'password' => 'Password123',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin_test@example.com',
            'password' => 'Password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_member_can_authenticate_and_redirects_to_member_dashboard(): void
    {
        $member = User::factory()->member()->create([
            'email' => 'member_test@example.com',
            'password' => 'Password123',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'member_test@example.com',
            'password' => 'Password123',
        ]);

        $this->assertAuthenticatedAs($member);
        $response->assertRedirect(route('member.dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user_test@example.com',
            'password' => 'Password123',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'user_test@example.com',
            'password' => 'WrongPassword',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => __('auth.failed')]);
    }

    public function test_non_existent_user_returns_generic_error_preventing_enumeration(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'Password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => __('auth.failed')]);
    }

    public function test_inactive_user_cannot_authenticate_and_gets_clear_message(): void
    {
        $inactiveUser = User::factory()->inactive()->create([
            'email' => 'inactive@example.com',
            'password' => 'Password123',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'Password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Your account is currently inactive. Please contact the administrator.',
        ]);
    }

    public function test_soft_deleted_user_cannot_authenticate_and_gets_clear_message(): void
    {
        $deletedUser = User::factory()->create([
            'email' => 'deleted@example.com',
            'password' => 'Password123',
            'deleted_at' => now(),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'deleted@example.com',
            'password' => 'Password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Your account has been deleted. Please contact the administrator.',
        ]);
    }

    public function test_login_attempts_are_rate_limited_to_5_per_minute(): void
    {
        Event::fake([Lockout::class]);

        $user = User::factory()->create([
            'email' => 'ratelimit@example.com',
            'password' => 'Password123',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'ratelimit@example.com',
                'password' => 'WrongPassword',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // 6th attempt should be blocked and fire Lockout event
        $response = $this->post('/login', [
            'email' => 'ratelimit@example.com',
            'password' => 'WrongPassword',
        ]);

        Event::assertDispatched(Lockout::class);
        $response->assertSessionHasErrors('email');

        $errorMessage = session('errors')->first('email');
        $this->assertMatchesRegularExpression('/seconds/i', $errorMessage);
    }

    public function test_rate_limiter_is_cleared_on_successful_login(): void
    {
        $user = User::factory()->create([
            'email' => 'clear_throttle@example.com',
            'password' => 'Password123',
        ]);

        // Failed attempts
        $this->post('/login', [
            'email' => 'clear_throttle@example.com',
            'password' => 'WrongPassword',
        ]);

        $throttleKey = 'clear_throttle@example.com|127.0.0.1';
        $this->assertGreaterThan(0, RateLimiter::attempts($throttleKey));

        // Successful login
        $this->post('/login', [
            'email' => 'clear_throttle@example.com',
            'password' => 'Password123',
        ]);

        $this->assertEquals(0, RateLimiter::attempts($throttleKey));
    }

    public function test_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_visiting_login_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_member_visiting_login_is_redirected_to_member_dashboard(): void
    {
        $member = User::factory()->member()->create();

        $response = $this->actingAs($member)->get('/login');

        $response->assertRedirect(route('member.dashboard'));
    }

    public function test_root_url_renders_landing_page_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Harmoni');
    }

    public function test_root_url_redirects_admin_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_root_url_redirects_member_to_member_dashboard(): void
    {
        $member = User::factory()->member()->create();

        $response = $this->actingAs($member)->get('/');

        $response->assertRedirect(route('member.dashboard'));
    }
}
