<?php

use App\Models\Assets;
use App\Models\Campuses;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function createEquipmentUpdateUser(int $roleId, Campuses $campus, string $username): User
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

function createEquipmentUpdateAsset(Campuses $campus): Assets
{
    $officeId = DB::table('offices')->insertGetId([
        'office_code' => 'EQP',
        'office_name' => 'Equipment Test Office',
        'office_acr' => 'EQP',
        'office_pap_code' => 'EQP-PAP',
        'office_pap_no' => 1,
        'office_show' => 1,
        'office_is_college' => 0,
        'office_is_one' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return Assets::create([
        'asset_name' => 'Equipment Test Hall',
        'asset_type' => 'venue',
        'capacity' => 120,
        'location' => 'Main Campus',
        'acquisition_date' => now()->toDateString(),
        'condition' => 'good',
        'campus_id' => $campus->id,
        'office_id' => $officeId,
        'availability_status' => 'available',
    ]);
}

function createEquipmentUpdateReservation(Assets $asset, User $submitter, string $status = 'PENDING'): Reservation
{
    $reservation = Reservation::create([
        'title_name' => 'Equipment Update Event',
        'asset_id' => $asset->id,
        'range' => 1,
        'time_start' => '09:00',
        'time_end' => '10:00',
        'description' => 'Reservation used to verify equipment updates.',
        'people_tag' => 'Requester',
        'info_type' => 'public',
        'category' => 'academic',
        'date' => now()->addWeek()->toDateString(),
        'reserved_by_user' => $submitter->id,
        'status' => $status,
        'current_stage' => $status === 'PENDING' ? 'campus_director' : strtolower($status),
        'multimedia_comment' => 'Internal equipment remark',
    ]);

    $reservation->equipment()->create([
        'name' => 'Microphones',
        'quantity' => 3,
        'note' => 'Initial internal note',
    ]);

    return $reservation;
}

it('allows multimedia to update pending equipment quantities and notes', function () {
    $campus = Campuses::create([
        'campus_name' => 'Main Campus',
        'campus_acr' => 'MAIN',
    ]);

    $multimedia = createEquipmentUpdateUser(11, $campus, 'equipment_multimedia');
    $submitter = createEquipmentUpdateUser(10, $campus, 'equipment_submitter');
    $asset = createEquipmentUpdateAsset($campus);
    $reservation = createEquipmentUpdateReservation($asset, $submitter);
    $equipment = $reservation->equipment()->first();

    $this->actingAs($multimedia, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}/equipment", [
            'multimedia_comment' => 'We can only provide partial equipment.',
            'equipment' => [
                [
                    'id' => $equipment->id,
                    'quantity' => 2,
                    'note' => 'Only 2 microphones are available.',
                ],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('reservation.multimedia_comment', 'We can only provide partial equipment.')
        ->assertJsonPath('reservation.equipment.0.quantity', 2)
        ->assertJsonPath('reservation.equipment.0.note', 'Only 2 microphones are available.');

    $reservation->refresh();
    $equipment->refresh();

    expect($reservation->multimedia_comment)->toBe('We can only provide partial equipment.')
        ->and($equipment->quantity)->toBe(2)
        ->and($equipment->note)->toBe('Only 2 microphones are available.');
});

it('rejects equipment updates from non multimedia accounts', function () {
    $campus = Campuses::create([
        'campus_name' => 'Main Campus',
        'campus_acr' => 'MAIN',
    ]);

    $admin = createEquipmentUpdateUser(3, $campus, 'equipment_admin');
    $submitter = createEquipmentUpdateUser(10, $campus, 'equipment_submitter_non_multi');
    $asset = createEquipmentUpdateAsset($campus);
    $reservation = createEquipmentUpdateReservation($asset, $submitter);
    $equipment = $reservation->equipment()->first();

    $this->actingAs($admin, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}/equipment", [
            'equipment' => [
                ['id' => $equipment->id, 'quantity' => 2, 'note' => null],
            ],
        ])
        ->assertForbidden();
});

it('rejects equipment updates after a reservation is no longer pending', function () {
    $campus = Campuses::create([
        'campus_name' => 'Main Campus',
        'campus_acr' => 'MAIN',
    ]);

    $multimedia = createEquipmentUpdateUser(11, $campus, 'equipment_multimedia_approved');
    $submitter = createEquipmentUpdateUser(10, $campus, 'equipment_submitter_approved');
    $asset = createEquipmentUpdateAsset($campus);
    $reservation = createEquipmentUpdateReservation($asset, $submitter, 'APPROVED');
    $equipment = $reservation->equipment()->first();

    $this->actingAs($multimedia, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}/equipment", [
            'equipment' => [
                ['id' => $equipment->id, 'quantity' => 2, 'note' => null],
            ],
        ])
        ->assertStatus(422);
});

it('does not expose multimedia equipment remarks on the public reservations endpoint', function () {
    $campus = Campuses::create([
        'campus_name' => 'Main Campus',
        'campus_acr' => 'MAIN',
    ]);

    $submitter = createEquipmentUpdateUser(10, $campus, 'equipment_public_submitter');
    $asset = createEquipmentUpdateAsset($campus);
    $reservation = createEquipmentUpdateReservation($asset, $submitter, 'APPROVED');

    $response = $this->getJson('/api/reservations/all');
    $response->assertOk();

    $publicReservation = collect($response->json())->firstWhere('id', $reservation->id);

    expect($publicReservation)->not->toBeNull()
        ->and($publicReservation)->not->toHaveKey('multimedia_comment')
        ->and($publicReservation['equipment'][0])->not->toHaveKey('note');
});
