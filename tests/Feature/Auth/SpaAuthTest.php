<?php

use App\Models\Campuses;
use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function createSpaAuthUser(string $username, int $roleId = 3): User
{
    $campus = Campuses::firstOrCreate(
        ['campus_acr' => 'SAT'],
        ['campus_name' => 'SPA Auth Test Campus']
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
        'last_name' => 'Tester',
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

function spaAuthResponseCookieNames(\Illuminate\Testing\TestResponse $response): array
{
    return collect($response->headers->getCookies())
        ->map(fn ($cookie) => $cookie->getName())
        ->all();
}

it('logs in with valid credentials, sets session + XSRF cookies, returns no token in the body', function () {
    /** @var \Tests\TestCase $this */
    createSpaAuthUser('spa_login_user', 3);

    $response = $this->withHeaders(['Referer' => 'http://localhost:3000/login'])
        ->postJson('/api/users/login', [
            'username' => 'spa_login_user',
            'password' => 'password',
        ]);

    $response->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'username', 'email', 'first_name', 'last_name'],
            'role',
            'expires_at',
        ])
        ->assertJsonMissingPath('token')
        ->assertJsonPath('role', 3);

    $cookieNames = spaAuthResponseCookieNames($response);

    expect($cookieNames)->toContain(config('session.cookie'))
        ->and($cookieNames)->toContain('XSRF-TOKEN');
});

it('returns 422 with the legacy error shape on wrong password', function () {
    /** @var \Tests\TestCase $this */
    createSpaAuthUser('spa_wrong_pw_user');

    $this->postJson('/api/users/login', [
        'username' => 'spa_wrong_pw_user',
        'password' => 'wrong-password',
    ])
        ->assertStatus(422)
        ->assertJsonStructure(['message', 'errors' => ['password']])
        ->assertJsonPath('errors.password.0', 'Password is incorrect.');
});

it('returns 422 with both username and password errors when username does not exist', function () {
    /** @var \Tests\TestCase $this */
    $this->postJson('/api/users/login', [
        'username' => 'unknown_user',
        'password' => 'password',
    ])
        ->assertStatus(422)
        ->assertJsonStructure(['message', 'errors' => ['username', 'password']]);
});

it('returns 401 on /api/me without a session', function () {
    /** @var \Tests\TestCase $this */
    $this->getJson('/api/me')
        ->assertUnauthorized();
});

it('returns user, role, and expires_at on /api/me with a valid session', function () {
    /** @var \Tests\TestCase $this */
    $user = createSpaAuthUser('spa_me_user', 5);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.username', 'spa_me_user')
        ->assertJsonPath('role', 5)
        ->assertJsonStructure(['user', 'role', 'expires_at']);
});

it('logs the user out and returns the legacy success message', function () {
    /** @var \Tests\TestCase $this */
    $user = createSpaAuthUser('spa_logout_user');

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/logout')
        ->assertOk()
        ->assertJson(['message' => 'Logged out successfully']);
});

it('invalidates the session so /api/me returns 401 after logout', function () {
    /** @var \Tests\TestCase $this */
    createSpaAuthUser('spa_lifecycle_user');

    // Real login over a stateful Referer so Sanctum injects the session pipeline.
    $this->withHeaders(['Referer' => 'http://localhost:3000/login'])
        ->postJson('/api/users/login', [
            'username' => 'spa_lifecycle_user',
            'password' => 'password',
        ])
        ->assertOk();

    // The AuthManager is a singleton across requests within a single test method,
    // so cached guard users would carry over and mask logout. Reset between calls
    // to mirror the per-request isolation that real HTTP traffic gets.
    $this->app['auth']->forgetGuards();

    $this->withHeaders(['Referer' => 'http://localhost:3000/dashboard'])
        ->getJson('/api/me')
        ->assertOk();

    $this->app['auth']->forgetGuards();

    $this->withHeaders(['Referer' => 'http://localhost:3000/dashboard'])
        ->postJson('/api/logout')
        ->assertOk();

    $this->app['auth']->forgetGuards();

    $this->withHeaders(['Referer' => 'http://localhost:3000/dashboard'])
        ->getJson('/api/me')
        ->assertUnauthorized();
});

it('wires Sanctum stateful + CSRF middleware onto api write routes', function () {
    /** @var \Tests\TestCase $this */
    // Laravel's VerifyCsrfToken short-circuits when $app->runningUnitTests()
    // returns true (see Illuminate\Foundation\Http\Middleware\VerifyCsrfToken),
    // so we can't observe a 419 from this test runner. Instead we assert the
    // stateful pipeline is actually attached to the api group + the route is
    // guarded by auth:sanctum — that combination injects StartSession +
    // VerifyCsrfToken on stateful SPA requests.
    $apiGroup = $this->app['router']->getMiddlewareGroups()['api'] ?? [];
    expect($apiGroup)->toContain(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class);

    $route = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
        ->first(fn ($r) => $r->uri() === 'api/event/reservation' && in_array('POST', $r->methods()));

    expect($route)->not->toBeNull()
        ->and($route->gatherMiddleware())->toContain('auth:sanctum');
});

it('rate limits login at 10 attempts per minute', function () {
    /** @var \Tests\TestCase $this */
    for ($i = 0; $i < 10; $i++) {
        $status = $this->postJson('/api/users/login', [
            'username' => 'rate_limit_user',
            'password' => 'wrong',
        ])->status();

        expect($status)->not->toBe(429);
    }

    $this->postJson('/api/users/login', [
        'username' => 'rate_limit_user',
        'password' => 'wrong',
    ])->assertStatus(429);
});
