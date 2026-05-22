<?php

use App\Models\Campuses;
use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function createSessionTouchUser(string $username, int $roleId = 3): User
{
    $campus = Campuses::firstOrCreate(
        ['campus_acr' => 'STT'],
        ['campus_name' => 'Session Touch Test Campus']
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

it('returns 200 with a fresh expires_at on /api/session/touch with a valid session', function () {
    /** @var \Tests\TestCase $this */
    createSessionTouchUser('session_touch_ok_user');

    $loginResponse = $this->withCredentials()
        ->withHeaders(['Referer' => 'http://localhost:3000/login'])
        ->postJson('/api/users/login', [
            'username' => 'session_touch_ok_user',
            'password' => 'password',
        ]);
    $loginResponse->assertOk();

    // Carry the session cookie forward — the JSON test client does not
    // auto-persist response cookies between calls.
    $cookieName = config('session.cookie');
    $sessionId = $loginResponse->getCookie($cookieName)->getValue();

    $this->app['auth']->forgetGuards();

    $this->withCredentials()
        ->withCookie($cookieName, $sessionId)
        ->withHeaders(['Referer' => 'http://localhost:3000/dashboard'])
        ->postJson('/api/session/touch')
        ->assertOk()
        ->assertJsonStructure(['message', 'expires_at']);
});

it('does NOT rotate the session ID on /api/session/touch (race-free)', function () {
    /** @var \Tests\TestCase $this */
    createSessionTouchUser('session_touch_stable_user');

    $loginResponse = $this->withCredentials()
        ->withHeaders(['Referer' => 'http://localhost:3000/login'])
        ->postJson('/api/users/login', [
            'username' => 'session_touch_stable_user',
            'password' => 'password',
        ]);
    $loginResponse->assertOk();

    $cookieName = config('session.cookie');
    $sessionIdBefore = $loginResponse->getCookie($cookieName)->getValue();

    $this->app['auth']->forgetGuards();

    $touchResponse = $this->withCredentials()
        ->withCookie($cookieName, $sessionIdBefore)
        ->withHeaders(['Referer' => 'http://localhost:3000/dashboard'])
        ->postJson('/api/session/touch');
    $touchResponse->assertOk();

    $sessionIdAfter = $touchResponse->getCookie($cookieName)->getValue();

    expect($sessionIdAfter)->toBe($sessionIdBefore)
        ->and($sessionIdBefore)->not->toBeEmpty();
});

it('returns 401 on /api/session/touch without a session', function () {
    /** @var \Tests\TestCase $this */
    $this->postJson('/api/session/touch')
        ->assertUnauthorized();
});
