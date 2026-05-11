<?php

use App\Models\Assets;
use App\Models\Campuses;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

function createRoleUser(int $roleId, Campuses $campus, string $username): User
{
    DB::table('roles')->updateOrInsert(
        ['id' => $roleId],
        [
            'role_name' => "ROLE_{$roleId}",
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    $user = User::create([
        'first_name' => ucfirst($username),
        'last_name' => 'User',
        'email' => "{$username}@example.test",
        'username' => $username,
        'password' => Hash::make('password'),
        'campus_id' => $campus->id,
    ]);

    UserRoles::create([
        'user_id' => $user->id,
        'role_id' => $roleId,
        'full_id' => 0,
    ]);

    return $user;
}

function createVPApprovalReservation(Campuses $campus, User $submitter): Reservation
{
    $officeId = DB::table('offices')->insertGetId([
        'office_code' => 'TST',
        'office_name' => 'Testing Office',
        'office_acr' => 'TST',
        'office_pap_code' => 'PAP',
        'office_pap_no' => 1,
        'office_show' => 1,
        'office_is_college' => 0,
        'office_is_one' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $asset = Assets::create([
        'asset_name' => 'Main Hall',
        'asset_type' => 'venue',
        'capacity' => 100,
        'location' => 'Main Campus',
        'acquisition_date' => now()->toDateString(),
        'condition' => 'good',
        'campus_id' => $campus->id,
        'office_id' => $officeId,
        'availability_status' => 'available',
    ]);

    return Reservation::create([
        'title_name' => 'Shared VP Approval Event',
        'asset_id' => $asset->id,
        'range' => 1,
        'time_start' => '09:00',
        'time_end' => '10:00',
        'description' => 'Reservation that requires multiple VP approvals.',
        'people_tag' => 'Requester',
        'info_type' => 'public',
        'category' => 'academic',
        'date' => now()->addWeek()->toDateString(),
        'reserved_by_user' => $submitter->id,
        'status' => 'PENDING',
        'requires_vpaa' => true,
        'requires_vpsas' => true,
        'current_stage' => 'vp_approval',
    ]);
}

it('removes only the VP who has approved from the shared VP approval queue', function () {
    Notification::fake();

    $campus = Campuses::create([
        'campus_name' => 'Main Campus',
        'campus_acr' => 'MAIN',
    ]);

    $vpaa = createRoleUser(6, $campus, 'vpaa');
    $vpsas = createRoleUser(7, $campus, 'vpsas');
    $submitter = createRoleUser(10, $campus, 'submitter');
    $reservation = createVPApprovalReservation($campus, $submitter);

    $vpaaQueue = $this->actingAs($vpaa, 'sanctum')->getJson('/api/reservations/queue');
    $vpaaQueue->assertOk();
    expect(collect($vpaaQueue->json())->pluck('id'))->toContain($reservation->id);

    $this->actingAs($vpaa, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}", [
            'action' => 'APPROVED',
            'approved_by_user' => $vpaa->id,
        ])
        ->assertOk();

    $reservation->refresh();
    expect($reservation->status)->toBe('PENDING')
        ->and($reservation->current_stage)->toBe('vp_approval');

    $vpaaQueueAfterApproval = $this->actingAs($vpaa, 'sanctum')->getJson('/api/reservations/queue');
    $vpaaQueueAfterApproval->assertOk();
    expect(collect($vpaaQueueAfterApproval->json())->pluck('id'))->not->toContain($reservation->id);

    $vpsasQueue = $this->actingAs($vpsas, 'sanctum')->getJson('/api/reservations/queue');
    $vpsasQueue->assertOk();
    expect(collect($vpsasQueue->json())->pluck('id'))->toContain($reservation->id);

    $this->actingAs($vpsas, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}", [
            'action' => 'APPROVED',
            'approved_by_user' => $vpsas->id,
        ])
        ->assertOk();

    $reservation->refresh();
    expect($reservation->status)->toBe('PENDING')
        ->and($reservation->current_stage)->toBe('campus_director');
});

it('rejects duplicate VP approval for the same reservation stage', function () {
    Notification::fake();

    $campus = Campuses::create([
        'campus_name' => 'Main Campus',
        'campus_acr' => 'MAIN',
    ]);

    $vpaa = createRoleUser(6, $campus, 'duplicate_vpaa');
    $submitter = createRoleUser(10, $campus, 'duplicate_submitter');
    $reservation = createVPApprovalReservation($campus, $submitter);

    $payload = [
        'action' => 'APPROVED',
        'approved_by_user' => $vpaa->id,
    ];

    $this->actingAs($vpaa, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}", $payload)
        ->assertOk();

    $this->actingAs($vpaa, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}", $payload)
        ->assertStatus(409);
});
