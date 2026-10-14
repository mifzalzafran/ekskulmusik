<?php

namespace Tests\Feature;

use App\Enums\InstrumentOwnership;
use App\Enums\InstrumentStatus;
use App\Enums\StudioBookingStatus;
use App\Enums\UserRole;
use App\Models\Instrument;
use App\Models\StudioBooking;
use App\Models\StudioRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_admin_dashboard_with_metrics(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Admin Control Center');
        $response->assertSee('Selamat Datang, ' . $admin->name);
    }

    public function test_member_can_view_member_dashboard_with_profile(): void
    {
        $member = User::factory()->member()->create([
            'is_active' => true,
            'class_name' => 'XI-IPA-1',
            'is_choir' => true,
        ]);

        $response = $this->actingAs($member)->get(route('member.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Halo, ' . $member->name);
        $response->assertSee('Tim Paduan Suara Upacara');
        $response->assertSee('Alat Musik Titipan Saya di Studio');
    }

    public function test_member_can_toggle_take_home_status_of_their_deposited_instrument(): void
    {
        $member = User::factory()->member()->create(['is_active' => true]);

        $instrument = Instrument::create([
            'name' => 'Gitar Akustik Pribadi',
            'category' => 'Strings',
            'ownership_type' => InstrumentOwnership::Student,
            'owner_user_id' => $member->id,
            'status' => InstrumentStatus::Available,
        ]);

        // Toggle to taken home
        $response = $this->actingAs($member)
            ->patch(route('member.instruments.toggle-take-home', $instrument));

        $response->assertRedirect();
        $this->assertEquals(InstrumentStatus::TakenHome, $instrument->fresh()->status);

        // Toggle back to available in studio
        $response2 = $this->actingAs($member)
            ->patch(route('member.instruments.toggle-take-home', $instrument));

        $response2->assertRedirect();
        $this->assertEquals(InstrumentStatus::Available, $instrument->fresh()->status);
    }

    public function test_admin_can_approve_pending_studio_booking(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => true]);
        $member = User::factory()->member()->create(['is_active' => true]);
        $room = StudioRoom::create(['name' => 'Studio Utama']);

        $booking = StudioBooking::create([
            'studio_room_id' => $room->id,
            'user_id' => $member->id,
            'booking_date' => now()->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'status' => StudioBookingStatus::Pending,
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.studio-bookings.status', $booking), [
                'status' => 'approved',
            ]);

        $response->assertRedirect();
        $this->assertEquals(StudioBookingStatus::Approved, $booking->fresh()->status);
        $this->assertEquals($admin->id, $booking->fresh()->approved_by);
    }
}
