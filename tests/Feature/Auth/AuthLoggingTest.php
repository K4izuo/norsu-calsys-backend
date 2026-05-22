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

function createAuthLoggingUser(string $username, int $roleId = 3): User
{
    $campus = Campuses::firstOrCreate(
        ['campus_acr' => 'ALT'],
        ['campus_name' => 'Auth Logging Test Campus']
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

/**
 * Build a Mockery channel mock that records exactly one info() call whose
 * $context['event'] equals $expectedEvent, plus an optional reason check.
 * Other Log facade methods pass through to the real implementation via partialMock.
 */
function expectAuthLog(string $expectedEvent, ?string $expectedReason = null): void
{
    $authChannel = Mockery::mock(LoggerInterface::class);
    $authChannel->shouldReceive('info')
        ->once()
        ->withArgs(function ($message, $context = []) use ($expectedEvent, $expectedReason) {
            if (($context['event'] ?? null) !== $expectedEvent) {
                return false;
            }
            if ($expectedReason !== null && ($context['reason'] ?? null) !== $expectedReason) {
                return false;
            }
            // Never accept a password or hash in log context.
            $forbidden = ['password', 'password_hash', 'new_password', 'current_password'];
            foreach ($forbidden as $key) {
                if (array_key_exists($key, $context)) {
                    return false;
                }
            }
            return true;
        });

    Log::partialMock()
        ->shouldReceive('channel')
        ->with('auth')
        ->andReturn($authChannel);
}

it('writes a login.success entry to the auth log on successful login', function () {
    /** @var \Tests\TestCase $this */
    createAuthLoggingUser('auth_log_ok_user', 3);

    expectAuthLog('login.success');

    $this->withCredentials()
        ->withHeaders(['Referer' => 'http://localhost:3000/login'])
        ->postJson('/api/users/login', [
            'username' => 'auth_log_ok_user',
            'password' => 'password',
        ])
        ->assertOk();
});

it('writes a login.failure entry with reason=invalid_password on wrong password', function () {
    /** @var \Tests\TestCase $this */
    createAuthLoggingUser('auth_log_wrong_pw_user');

    expectAuthLog('login.failure', 'invalid_password');

    $this->postJson('/api/users/login', [
        'username' => 'auth_log_wrong_pw_user',
        'password' => 'definitely-wrong',
    ])->assertStatus(422);
});
