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

function createRequestorPersistenceRoleUser(int $roleId, Campuses $campus, string $username): User
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

function createRequestorPersistenceAsset(Campuses $campus): Assets
{
    $officeId = DB::table('offices')->insertGetId([
        'office_code' => 'REQ',
        'office_name' => 'Requestor Test Office',
        'office_acr' => 'REQ',
        'office_pap_code' => 'REQ-PAP',
        'office_pap_no' => 1,
        'office_show' => 1,
        'office_is_college' => 0,
        'office_is_one' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return Assets::create([
        'asset_name' => 'Requestor Test Hall',
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

function makeRequestorPersistencePayload(Assets $asset, User $submitter, array $overrides = []): array
{
    return array_merge([
        'title_name' => 'Requestor Persistence Event',
        'asset_id' => $asset->id,
        'range' => 1,
        'time_start' => '09:00',
        'time_end' => '10:00',
        'description' => 'Reservation used to verify requestor persistence.',
        'people_tag' => 'Requester',
        'info_type' => 'public',
        'category' => 'academic',
        'date' => now()->addWeek()->toDateString(),
        'reserved_by_user' => $submitter->id,
    ], $overrides);
}

it('stores and returns requestor data for new reservations', function () {
    Notification::fake();

    $campus = Campuses::create([
        'campus_name' => 'Main Campus',
        'campus_acr' => 'MAIN',
    ]);
    $submitter = createRequestorPersistenceRoleUser(12, $campus, 'requestor_submitter');
    $asset = createRequestorPersistenceAsset($campus);

    $cases = [
        [
            'requestor_type' => 'student',
            'student_sub_type' => 'student_org',
            'student_org_name' => 'Math Society',
            'csg_name' => null,
            'requestor_tagged' => null,
        ],
        [
            'requestor_type' => 'faculty',
            'student_sub_type' => null,
            'student_org_name' => null,
            'csg_name' => null,
            'requestor_tagged' => [['id' => 4, 'name' => 'College of Arts and Sciences']],
        ],
        [
            'requestor_type' => 'office',
            'student_sub_type' => null,
            'student_org_name' => null,
            'csg_name' => null,
            'requestor_tagged' => [['id' => 9, 'name' => 'Registrar Office']],
        ],
    ];

    $reservationIds = [];

    foreach ($cases as $index => $requestorFields) {
        $response = $this->actingAs($submitter, 'sanctum')
            ->postJson('/api/event/reservation', makeRequestorPersistencePayload($asset, $submitter, array_merge(
                ['title_name' => "Requestor Persistence Event {$index}"],
                $requestorFields
            )));

        $response->assertCreated();
        $reservationIds[] = $response->json('reservation.id');
    }

    $indexResponse = $this->getJson('/api/reservations/all');
    $indexResponse->assertOk();
    $reservations = collect($indexResponse->json());

    foreach ($cases as $index => $requestorFields) {
        $reservation = $reservations->firstWhere('id', $reservationIds[$index]);

        expect($reservation)->not->toBeNull()
            ->and($reservation['requestor_type'])->toBe($requestorFields['requestor_type'])
            ->and($reservation['student_sub_type'])->toBe($requestorFields['student_sub_type'])
            ->and($reservation['student_org_name'])->toBe($requestorFields['student_org_name'])
            ->and($reservation['csg_name'])->toBe($requestorFields['csg_name'])
            ->and($reservation['requestor_tagged'])->toBe($requestorFields['requestor_tagged']);
    }
});

it('returns updated requestor data when a declined reservation is resubmitted', function () {
    Notification::fake();

    $campus = Campuses::create([
        'campus_name' => 'Main Campus',
        'campus_acr' => 'MAIN',
    ]);
    $submitter = createRequestorPersistenceRoleUser(12, $campus, 'requestor_resubmitter');
    $asset = createRequestorPersistenceAsset($campus);

    $reservation = Reservation::create(makeRequestorPersistencePayload($asset, $submitter, [
        'status' => 'DECLINED',
        'current_stage' => 'declined',
        'declined_at_stage' => 'campus_director',
        'requestor_type' => 'student',
        'student_sub_type' => 'csg',
        'csg_name' => 'Old CSG',
    ]));

    $response = $this->actingAs($submitter, 'sanctum')
        ->postJson("/api/reservations/{$reservation->id}/resubmit", makeRequestorPersistencePayload($asset, $submitter, [
            'title_name' => 'Resubmitted Requestor Event',
            'requestor_type' => 'office',
            'student_sub_type' => null,
            'student_org_name' => null,
            'csg_name' => null,
            'requestor_tagged' => [['id' => 3, 'name' => 'Library Office']],
        ]));

    $response->assertOk()
        ->assertJsonPath('reservation.status', 'PENDING')
        ->assertJsonPath('reservation.current_stage', 'campus_director')
        ->assertJsonPath('reservation.requestor_type', 'office')
        ->assertJsonPath('reservation.requestor_tagged.0.name', 'Library Office');

    $reservation->refresh();

    expect($reservation->requestor_type)->toBe('office')
        ->and($reservation->student_sub_type)->toBeNull()
        ->and($reservation->csg_name)->toBeNull()
        ->and($reservation->requestor_tagged)->toBe([['id' => 3, 'name' => 'Library Office']]);
});
