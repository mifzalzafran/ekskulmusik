<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_password_change_screen(): void
    {
        $response = $this->get('/password/change');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_password_change_screen(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get('/password/change');

        $response->assertStatus(200);
        $response->assertSee('Change Password');
    }

    public function test_password_cannot_be_changed_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword123',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->from('/password/change')->put('/password/change', [
            'current_password' => 'WrongCurrent123',
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ]);

        $response->assertRedirect('/password/change');
        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));
    }

    public function test_password_cannot_be_changed_with_weak_new_password(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword123',
            'is_active' => true,
        ]);

        // Too short (< 8 chars)
        $response = $this->actingAs($user)->put('/password/change', [
            'current_password' => 'OldPassword123',
            'password' => 'short1',
            'password_confirmation' => 'short1',
        ]);
        $response->assertSessionHasErrors('password');

        // Letters only (no numbers)
        $response = $this->actingAs($user)->put('/password/change', [
            'current_password' => 'OldPassword123',
            'password' => 'lettersallonly',
            'password_confirmation' => 'lettersallonly',
        ]);
        $response->assertSessionHasErrors('password');

        // Numbers only (no letters)
        $response = $this->actingAs($user)->put('/password/change', [
            'current_password' => 'OldPassword123',
            'password' => '1234567890',
            'password_confirmation' => '1234567890',
        ]);
        $response->assertSessionHasErrors('password');
    }

    public function test_password_cannot_be_changed_if_confirmation_does_not_match(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword123',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put('/password/change', [
            'current_password' => 'OldPassword123',
            'password' => 'NewPassword456',
            'password_confirmation' => 'Mismatch456',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));
    }

    public function test_password_can_be_changed_successfully(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword123',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->from('/password/change')->put('/password/change', [
            'current_password' => 'OldPassword123',
            'password' => 'NewSecurePassword789',
            'password_confirmation' => 'NewSecurePassword789',
        ]);

        $response->assertRedirect('/password/change');
        $response->assertSessionHas('status', 'Password changed successfully.');
        $this->assertTrue(Hash::check('NewSecurePassword789', $user->fresh()->password));
    }
}
