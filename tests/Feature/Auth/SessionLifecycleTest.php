<?php

use App\Models\Campuses;
use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

uses(RefreshDatabase::class);

function createLifecycleUser(string $username, int $roleId = 3): User
{
    $campus = Campuses::firstOrCreate(
        ['campus_acr' => 'SLT'],
        ['campus_name' => 'Session Lifecycle Test Campus']
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

it('walks the full session lifecycle: login -> me -> protected write -> touch -> logout -> 401', function () {
    /** @var \Tests\TestCase $this */
    $user = createLifecycleUser('lifecycle_user', 3);

    // Capture every Log::channel('auth')->info(...) call into a recording array
    // so we can assert on the audit trail in step 8.
    $recorded = [];
    $authChannel = Mockery::mock(LoggerInterface::class);
    $authChannel->shouldReceive('info')
        ->zeroOrMoreTimes()
        ->andReturnUsing(function ($message, $context = []) use (&$recorded) {
            $recorded[] = $context;
        });
    Log::partialMock()
        ->shouldReceive('channel')
        ->with('auth')
        ->andReturn($authChannel);

    $cookieName = config('session.cookie');

    // ── Step 1: login ────────────────────────────────────────────────────
    $loginResponse = $this->withCredentials()
        ->withHeaders(['Referer' => 'http://localhost:3000/login'])
        ->postJson('/api/users/login', [
            'username' => 'lifecycle_user',
            'password' => 'password',
        ]);
    $loginResponse->assertOk()
        ->assertJsonStructure(['user', 'role', 'expires_at'])
        ->assertJsonMissingPath('token');

    $sessionId = $loginResponse->getCookie($cookieName)->getValue();
    expect($sessionId)->not->toBeEmpty();

    // XSRF cookie should also be issued by the stateful pipeline.
    $cookieNames = collect($loginResponse->headers->getCookies())->map(fn ($c) => $c->getName())->all();
    expect($cookieNames)->toContain('XSRF-TOKEN');

    $this->app['auth']->forgetGuards();

    // ── Step 2: /me returns the logged-in user ───────────────────────────
    $meResponse = $this->withCredentials()
        ->withCookie($cookieName, $sessionId)
        ->withHeaders(['Referer' => 'http://localhost:3000/dashboard'])
        ->getJson('/api/me');
    $meResponse->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.username', 'lifecycle_user')
        ->assertJsonPath('role', 3);

    $this->app['auth']->forgetGuards();

    // ── Step 3: protected POST (people store, requires only auth) ────────
    $peopleResponse = $this->withCredentials()
        ->withCookie($cookieName, $sessionId)
        ->withHeaders(['Referer' => 'http://localhost:3000/people'])
        ->postJson('/api/people', [
            'personName' => 'Lifecycle Guest',
        ]);
    $peopleResponse->assertStatus(201);

    // ── Step 4: session cookie did NOT rotate between login and write ───
    $sessionIdAfterWrite = $peopleResponse->getCookie($cookieName)->getValue();
    expect($sessionIdAfterWrite)->toBe($sessionId);

    $this->app['auth']->forgetGuards();

    // ── Step 5: /session/touch → 200 + cookie still unchanged ────────────
    $touchResponse = $this->withCredentials()
        ->withCookie($cookieName, $sessionId)
        ->withHeaders(['Referer' => 'http://localhost:3000/dashboard'])
        ->postJson('/api/session/touch');
    $touchResponse->assertOk()
        ->assertJsonStructure(['message', 'expires_at']);

    $sessionIdAfterTouch = $touchResponse->getCookie($cookieName)->getValue();
    expect($sessionIdAfterTouch)->toBe($sessionId);

    $this->app['auth']->forgetGuards();

    // ── Step 6: logout → 200 ─────────────────────────────────────────────
    $this->withCredentials()
        ->withCookie($cookieName, $sessionId)
        ->withHeaders(['Referer' => 'http://localhost:3000/dashboard'])
        ->postJson('/api/logout')
        ->assertOk()
        ->assertJson(['message' => 'Logged out successfully']);

    $this->app['auth']->forgetGuards();

    // ── Step 7: /me with the now-invalidated session ID → 401 ───────────
    // We deliberately replay the OLD session ID; the server should refuse to
    // resurrect the destroyed session.
    $this->withCredentials()
        ->withCookie($cookieName, $sessionId)
        ->withHeaders(['Referer' => 'http://localhost:3000/dashboard'])
        ->getJson('/api/me')
        ->assertUnauthorized();

    // ── Step 8: auth.log contains login.success and logout ──────────────
    $events = collect($recorded)->pluck('event')->filter()->all();

    expect($events)->toContain('login.success')
        ->and($events)->toContain('logout');

    // Sanity: the login.success entry carries the right user identity.
    $loginEntry = collect($recorded)->firstWhere('event', 'login.success');
    expect($loginEntry['user_id'])->toBe($user->id)
        ->and($loginEntry['username'])->toBe('lifecycle_user');
});
