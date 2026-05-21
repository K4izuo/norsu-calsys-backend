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

function createRoleMiddlewareUser(int $roleId, string $username): User
{
    $campus = Campuses::firstOrCreate(
        ['campus_acr' => 'RMT'],
        ['campus_name' => 'Role Middleware Test Campus']
    );

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

function createPendingReservationForRoleTest(): Reservation
{
    $campus = Campuses::create([
        'campus_name' => 'Role Test Campus',
        'campus_acr' => 'RTC',
    ]);

    $officeId = DB::table('offices')->insertGetId([
        'office_code' => 'RTO',
        'office_name' => 'Role Test Office',
        'office_acr' => 'RTO',
        'office_pap_code' => 'RTO-PAP',
        'office_pap_no' => 1,
        'office_show' => 1,
        'office_is_college' => 0,
        'office_is_one' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $submitter = createRoleMiddlewareUser(10, 'role_test_submitter');

    $asset = Assets::create([
        'asset_name' => 'Role Test Venue',
        'asset_type' => 'venue',
        'capacity' => 50,
        'location' => 'Main',
        'acquisition_date' => now()->toDateString(),
        'condition' => 'good',
        'campus_id' => $campus->id,
        'office_id' => $officeId,
        'availability_status' => 'available',
    ]);

    return Reservation::create([
        'title_name' => 'Role Test Event',
        'asset_id' => $asset->id,
        'range' => 1,
        'time_start' => '09:00',
        'time_end' => '10:00',
        'description' => 'Reservation used for role middleware tests.',
        'people_tag' => 'Requester',
        'info_type' => 'public',
        'category' => 'academic',
        'date' => now()->addWeek()->toDateString(),
        'reserved_by_user' => $submitter->id,
        'status' => 'PENDING',
        'current_stage' => 'campus_director',
    ]);
}

it('allows admin (role 3) to list users', function () {
    /** @var \Tests\TestCase $this */
    $admin = createRoleMiddlewareUser(3, 'role_test_admin');

    $this->actingAs($admin, 'sanctum')
        ->getJson('/api/users/all')
        ->assertOk();
});

it('blocks staff (role 2) from listing users with 403', function () {
    /** @var \Tests\TestCase $this */
    $staff = createRoleMiddlewareUser(2, 'role_test_staff');

    $this->actingAs($staff, 'sanctum')
        ->getJson('/api/users/all')
        ->assertForbidden()
        ->assertJson(['message' => 'Forbidden: insufficient role']);
});

it('rejects unauthenticated requests to users/all with 401', function () {
    /** @var \Tests\TestCase $this */
    $this->getJson('/api/users/all')
        ->assertUnauthorized();
});

it('lets a review role (campus director) past the reservation update middleware', function () {
    /** @var \Tests\TestCase $this */
    $reservation = createPendingReservationForRoleTest();
    $campusDirector = createRoleMiddlewareUser(5, 'role_test_campus_director');

    $response = $this->actingAs($campusDirector, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}", [
            'action' => 'APPROVE',
        ]);

    expect($response->status())->not->toBe(403);
});

it('blocks staff (role 2) from updating reservations with 403', function () {
    /** @var \Tests\TestCase $this */
    $reservation = createPendingReservationForRoleTest();
    $staff = createRoleMiddlewareUser(2, 'role_test_staff_update');

    $this->actingAs($staff, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}", [
            'action' => 'APPROVE',
        ])
        ->assertForbidden()
        ->assertJson(['message' => 'Forbidden: insufficient role']);
});

it('blocks staff (role 2) from moving reservations with 403', function () {
    /** @var \Tests\TestCase $this */
    $reservation = createPendingReservationForRoleTest();
    $staff = createRoleMiddlewareUser(2, 'role_test_staff_move');

    $this->actingAs($staff, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}/move", [])
        ->assertForbidden()
        ->assertJson(['message' => 'Forbidden: insufficient role']);
});

it('lets a campus director (role 5) past the reservation move middleware', function () {
    /** @var \Tests\TestCase $this */
    $reservation = createPendingReservationForRoleTest();
    $campusDirector = createRoleMiddlewareUser(5, 'role_test_move_director');

    $response = $this->actingAs($campusDirector, 'sanctum')
        ->putJson("/api/reservations/{$reservation->id}/move", []);

    expect($response->status())->not->toBe(403);
});
