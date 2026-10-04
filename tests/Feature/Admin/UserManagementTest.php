<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'is_active' => true,
        ]);
    }

    public function test_user_index_shows_only_show_and_restore_for_soft_deleted_users(): void
    {
        $otherAdmin = User::factory()->admin()->create(['is_active' => true]);
        $activeMember = User::factory()->member()->create(['is_active' => true, 'is_choir' => false]);
        $trashedMember = User::factory()->member()->create(['name' => 'Trashed Member', 'is_active' => true]);
        $trashedMember->delete();

        $response = $this->actingAs($this->admin)->get(route('admin.users.index', ['show_deleted' => '1']));

        $response->assertStatus(200);
        $response->assertSee('Trashed Member');

        // For trashed member, restore button is present and show is present
        $response->assertSee(route('admin.users.restore', $trashedMember));
        $response->assertSee(route('admin.users.show', $trashedMember));

        // For trashed member, edit, toggle-active, and toggle-choir are not linked
        $response->assertDontSee(route('admin.users.edit', $trashedMember));
        $response->assertDontSee(route('admin.users.toggle-active', $trashedMember));
        $response->assertDontSee(route('admin.users.toggle-choir', $trashedMember));
        // And delete form is not rendered for trashed member
        $this->assertStringNotContainsString('action="' . route('admin.users.show', $trashedMember) . '" method="POST" style="display:inline;" onsubmit="return confirm', $response->getContent());
    }

    public function test_soft_deleted_user_cannot_be_edited_via_url(): void
    {
        $trashedUser = User::factory()->member()->create();
        $trashedUser->delete();

        $response = $this->actingAs($this->admin)->get(route('admin.users.edit', $trashedUser));

        $response->assertStatus(403);
    }

    public function test_soft_deleted_user_cannot_be_updated_via_url(): void
    {
        $trashedUser = User::factory()->member()->create(['name' => 'Original Name']);
        $trashedUser->delete();

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $trashedUser), [
            'name' => 'Updated Name',
            'email' => $trashedUser->email,
            'role' => UserRole::Member->value,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('Original Name', $trashedUser->fresh()->name);
    }

    public function test_soft_deleted_user_cannot_be_deleted_again(): void
    {
        $trashedUser = User::factory()->member()->create();
        $trashedUser->delete();

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $trashedUser));

        $response->assertSessionHas('error');
    }

    public function test_soft_deleted_user_cannot_be_toggled_active(): void
    {
        $trashedUser = User::factory()->member()->create(['is_active' => true]);
        $trashedUser->delete();

        $response = $this->actingAs($this->admin)->patch(route('admin.users.toggle-active', $trashedUser));

        $response->assertSessionHas('error');
        $this->assertTrue($trashedUser->fresh()->is_active);
    }

    public function test_soft_deleted_user_cannot_be_toggled_choir(): void
    {
        $trashedUser = User::factory()->member()->create(['is_choir' => false]);
        $trashedUser->delete();

        $response = $this->actingAs($this->admin)->patch(route('admin.users.toggle-choir', $trashedUser));

        $response->assertSessionHas('error');
        $this->assertFalse($trashedUser->fresh()->is_choir);
    }

    public function test_soft_deleted_user_can_be_viewed_and_restored(): void
    {
        $trashedUser = User::factory()->member()->create();
        $trashedUser->delete();

        // Admin can view details of soft deleted user
        $responseShow = $this->actingAs($this->admin)->get(route('admin.users.show', $trashedUser));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Restore User');
        $responseShow->assertDontSee('Delete User');

        // Admin can restore soft deleted user
        $responseRestore = $this->actingAs($this->admin)->patch(route('admin.users.restore', $trashedUser));
        $responseRestore->assertRedirect(route('admin.users.index'));
        $this->assertFalse($trashedUser->fresh()->trashed());
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        User::factory()->admin()->create(['is_active' => true]); // second admin

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));

        $response->assertSessionHas('error');
        $this->assertFalse($this->admin->fresh()->trashed());
    }

    public function test_admin_cannot_deactivate_own_account_via_toggle(): void
    {
        User::factory()->admin()->create(['is_active' => true]); // second admin

        $response = $this->actingAs($this->admin)->patch(route('admin.users.toggle-active', $this->admin));

        $response->assertSessionHas('error');
        $this->assertTrue($this->admin->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_own_account_via_update_form(): void
    {
        User::factory()->admin()->create(['is_active' => true]); // second admin

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'name' => 'Admin Edited',
            'email' => $this->admin->email,
            'role' => UserRole::Admin->value,
            'is_active' => 0,
        ]);

        $response->assertSessionHas('error');
        $this->assertTrue($this->admin->fresh()->is_active);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        User::factory()->admin()->create(['is_active' => true]); // second admin

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin), [
            'name' => 'Admin Demote Attempt',
            'email' => $this->admin->email,
            'role' => UserRole::Member->value,
            'is_active' => 1,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(UserRole::Admin, $this->admin->fresh()->role);
    }

    public function test_last_admin_cannot_be_deleted(): void
    {
        // Only $this->admin exists
        $otherAdmin = User::factory()->admin()->create(['is_active' => true]);

        // Login as otherAdmin and try to delete the only other admin ($this->admin is active)
        // If we delete otherAdmin first, $this->admin is last admin
        $otherAdmin->delete();

        // Create temporary second admin who attempts to delete the last active original admin
        $tempAdmin = User::factory()->admin()->create(['is_active' => true]);

        // If $this->admin is the only other admin, delete tempAdmin first so only 1 admin remains:
        // Actually: if $tempAdmin tries to delete $this->admin, but $tempAdmin is also admin, that's 2 admins.
        // But if there is only 1 admin overall, that admin can't be deleted.
        // What if an admin tries to delete the last active admin?
        // Suppose tempAdmin is inactive admin:
        $tempAdmin->update(['is_active' => false]);
        // Now $this->admin is the ONLY active admin remaining!
        // A direct policy check or attempt to delete $this->admin:
        // Let's test with UserPolicy directly or via actingAs:
        $this->actingAs($this->admin);
        $this->assertTrue(
            $this->admin->can('delete', User::factory()->member()->create())
        );

        // If an admin is the last active admin:
        $this->assertTrue($this->admin->isAdmin());
        $this->assertFalse(
            (new \App\Policies\UserPolicy())->delete($tempAdmin, $this->admin)->allowed()
        );
    }

    public function test_last_active_admin_cannot_be_deactivated(): void
    {
        // $this->admin is the only admin
        $secondAdmin = User::factory()->admin()->create(['is_active' => false]);

        // Attempting to deactivate $this->admin by $secondAdmin (or direct policy check)
        $policy = new \App\Policies\UserPolicy();
        $response = $policy->deactivate($secondAdmin, $this->admin);

        $this->assertFalse($response->allowed());
        $this->assertStringContainsString('admin terakhir', $response->message());
    }

    public function test_inactive_and_soft_deleted_user_cannot_login(): void
    {
        $password = 'Secret123!';

        // Inactive member
        $inactive = User::factory()->member()->create([
            'email' => 'inactive_user@example.com',
            'password' => $password,
            'is_active' => false,
        ]);

        $responseInactive = $this->post('/login', [
            'email' => 'inactive_user@example.com',
            'password' => $password,
        ]);

        $responseInactive->assertSessionHasErrors('email');
        $this->assertGuest();

        // Soft deleted member
        $deleted = User::factory()->member()->create([
            'email' => 'deleted_user@example.com',
            'password' => $password,
            'is_active' => true,
        ]);
        $deleted->delete();

        $responseDeleted = $this->post('/login', [
            'email' => 'deleted_user@example.com',
            'password' => $password,
        ]);

        $responseDeleted->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
