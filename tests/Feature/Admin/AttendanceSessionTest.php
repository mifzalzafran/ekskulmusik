<?php

namespace Tests\Feature\Admin;

use App\Enums\AttendanceSessionType;
use App\Enums\AttendanceStatus;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceSessionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create(['is_active' => true]);
        $this->member = User::factory()->member()->create(['is_active' => true]);
    }

    public function test_admin_can_view_attendance_sessions_list(): void
    {
        $session = AttendanceSession::factory()->create([
            'title' => 'Latihan Rutin Band',
            'session_type' => AttendanceSessionType::Latihan,
            'is_open' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.attendance-sessions.index'));

        $response->assertStatus(200);
        $response->assertSee('Latihan Rutin Band');
        $response->assertSee('Latihan');
    }

    public function test_admin_can_create_attendance_session(): void
    {
        $payload = [
            'title' => 'Gladi Bersih Aubade',
            'session_type' => AttendanceSessionType::Upacara->value,
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '07:00',
            'end_time' => '08:30',
            'location' => 'Lapangan Upacara',
            'description' => 'Persiapan Aubade hari Senin.',
            'is_open' => 1,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.attendance-sessions.store'), $payload);

        $session = AttendanceSession::where('title', 'Gladi Bersih Aubade')->first();
        $this->assertNotNull($session);
        $this->assertEquals(AttendanceSessionType::Upacara, $session->session_type);
        $this->assertEquals($this->admin->id, $session->created_by);

        $response->assertRedirect(route('admin.attendance-sessions.show', $session));
    }

    public function test_admin_can_view_session_detail_and_members_list(): void
    {
        $session = AttendanceSession::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.attendance-sessions.show', $session));

        $response->assertStatus(200);
        $response->assertSee($session->title);
        $response->assertSee($this->member->name);
    }

    public function test_admin_can_update_attendance_session(): void
    {
        $session = AttendanceSession::factory()->create(['title' => 'Old Title']);

        $response = $this->actingAs($this->admin)->put(route('admin.attendance-sessions.update', $session), [
            'title' => 'New Title',
            'session_type' => AttendanceSessionType::Forum->value,
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('admin.attendance-sessions.show', $session));
        $this->assertEquals('New Title', $session->fresh()->title);
        $this->assertEquals(AttendanceSessionType::Forum, $session->fresh()->session_type);
    }

    public function test_admin_can_delete_attendance_session(): void
    {
        $session = AttendanceSession::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.attendance-sessions.destroy', $session));

        $response->assertRedirect(route('admin.attendance-sessions.index'));
        $this->assertDatabaseMissing('attendance_sessions', ['id' => $session->id]);
    }

    public function test_admin_can_toggle_open_status_of_attendance_session(): void
    {
        $session = AttendanceSession::factory()->create(['is_open' => true]);

        $response = $this->actingAs($this->admin)->patch(route('admin.attendance-sessions.toggle-open', $session));

        $response->assertSessionHas('status');
        $this->assertFalse($session->fresh()->is_open);

        $this->actingAs($this->admin)->patch(route('admin.attendance-sessions.toggle-open', $session));
        $this->assertTrue($session->fresh()->is_open);
    }

    public function test_admin_can_batch_record_member_attendance(): void
    {
        $session = AttendanceSession::factory()->create(['is_open' => true]);
        $member2 = User::factory()->member()->create(['is_active' => true]);

        $payload = [
            'attendances' => [
                [
                    'user_id' => $this->member->id,
                    'status' => AttendanceStatus::Present->value,
                    'notes' => 'Tepat waktu',
                ],
                [
                    'user_id' => $member2->id,
                    'status' => AttendanceStatus::Late->value,
                    'notes' => 'Terlambat 10 menit',
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.attendance-sessions.record', $session), $payload);

        $response->assertSessionHas('status');

        $record1 = AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('user_id', $this->member->id)
            ->first();
        $this->assertNotNull($record1);
        $this->assertEquals(AttendanceStatus::Present, $record1->status);
        $this->assertEquals('Tepat waktu', $record1->notes);
        $this->assertEquals($this->admin->id, $record1->recorded_by);

        $record2 = AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('user_id', $member2->id)
            ->first();
        $this->assertNotNull($record2);
        $this->assertEquals(AttendanceStatus::Late, $record2->status);
    }

    public function test_cannot_record_attendance_when_session_is_closed(): void
    {
        $session = AttendanceSession::factory()->closed()->create();

        $payload = [
            'attendances' => [
                [
                    'user_id' => $this->member->id,
                    'status' => AttendanceStatus::Present->value,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.attendance-sessions.record', $session), $payload);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('attendance_records', [
            'attendance_session_id' => $session->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_member_cannot_access_admin_attendance_routes(): void
    {
        $session = AttendanceSession::factory()->create();

        $this->actingAs($this->member)
            ->get(route('admin.attendance-sessions.index'))
            ->assertStatus(403);

        $this->actingAs($this->member)
            ->post(route('admin.attendance-sessions.store'), ['title' => 'Hack'])
            ->assertStatus(403);

        $this->actingAs($this->member)
            ->delete(route('admin.attendance-sessions.destroy', $session))
            ->assertStatus(403);
    }

    public function test_member_can_view_their_own_attendance_history_and_schedule(): void
    {
        $pastSession = AttendanceSession::factory()->create([
            'title' => 'Latihan Lagu Kebangsaan',
            'date' => now()->subDay()->toDateString(),
        ]);

        AttendanceRecord::factory()->create([
            'attendance_session_id' => $pastSession->id,
            'user_id' => $this->member->id,
            'status' => AttendanceStatus::Present,
            'notes' => 'Bagus',
        ]);

        $response = $this->actingAs($this->member)->get(route('member.attendance'));

        $response->assertStatus(200);
        $response->assertSee('Latihan Lagu Kebangsaan');
        $response->assertSee('Hadir');
        $response->assertSee('Bagus');
    }
}
