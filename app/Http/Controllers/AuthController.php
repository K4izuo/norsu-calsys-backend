<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthController extends Controller
{
  // Note: personal_access_tokens table retained empty in case a mobile/external
  // consumer ever needs token mode. SPA frontend uses session cookies only.

  public function login(Request $request)
  {
    $fields = $request->validate([
      'username' => 'required|string|max:255',
      'password' => 'required|string',
    ]);

    $user = User::where('username', $fields['username'])->first();

    // Username doesn't exist
    if (!$user) {
      $this->logAuth('Login failed: unknown username', [
        'event' => 'login.failure',
        'username' => $fields['username'],
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'reason' => 'invalid_username',
      ]);

      return $this->errorResponse('The provided credentials are incorrect.', [
        'username' => ['The provided credentials are incorrect.'],
        'password' => ['The provided credentials are incorrect.']
      ]);
    }

    // Password is incorrect
    if (!Hash::check($fields['password'], $user->password)) {
      $this->logAuth('Login failed: wrong password', [
        'event' => 'login.failure',
        'username' => $user->username,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'reason' => 'invalid_password',
      ]);

      return $this->errorResponse('Password is incorrect.', [
        'password' => ['Password is incorrect.']
      ]);
    }

    // Get user role
    $userRole = UserRoles::where('user_id', $user->id)->first();

    if (!$userRole) {
      $this->logAuth('Login failed: missing role', [
        'event' => 'login.failure',
        'username' => $user->username,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'reason' => 'no_role',
      ]);

      return $this->errorResponse('User role not found.', [
        'username' => ['User role configuration is missing.']
      ]);
    }

    // Establish a session (SPA cookie auth) and rotate the session ID
    // to defend against session fixation.
    Auth::guard('web')->login($user);
    $request->session()->regenerate();

    $this->logAuth('Login successful', [
      'event' => 'login.success',
      'user_id' => $user->id,
      'username' => $user->username,
      'ip' => $request->ip(),
      'user_agent' => $request->userAgent(),
    ]);

    return response()->json([
      'role' => $userRole->role_id,
      'user' => [
        'id' => $user->id,
        'username' => $user->username,
        'email' => $user->email,
        'first_name' => $user->first_name ?? '',
        'last_name' => $user->last_name ?? '',
      ],
      'expires_at' => $this->sessionExpiresAt(),
    ], 200);
  }

  public function logout(Request $request)
  {
    // Capture identity BEFORE Auth::guard('web')->logout() nulls the user.
    $userId = $request->user()?->id;

    Auth::guard('web')->logout();

    // Only touch the session when one is actually bound to the request
    // (stateful SPA call). Non-stateful callers — e.g. a one-off API
    // probe — should not crash logout just because there is no session.
    if ($request->hasSession()) {
      $request->session()->invalidate();
      $request->session()->regenerateToken();
    }

    $this->logAuth('User logged out', [
      'event' => 'logout',
      'user_id' => $userId,
      'ip' => $request->ip(),
    ]);

    return response()->json(['message' => 'Logged out successfully'], 200);
  }

  public function logoutAll(Request $request)
  {
    $userId = $request->user()?->id;

    // SPA mode: a single browser holds one session; treat this as a regular logout.
    Auth::guard('web')->logout();

    if ($request->hasSession()) {
      $request->session()->invalidate();
      $request->session()->regenerateToken();
    }

    $this->logAuth('User logged out (all devices)', [
      'event' => 'logout',
      'user_id' => $userId,
      'ip' => $request->ip(),
    ]);

    return response()->json(['message' => 'Logged out from all devices successfully'], 200);
  }

  public function me(Request $request)
  {
    $user = $request->user();
    $userRole = UserRoles::where('user_id', $user->id)->first();

    return response()->json([
      'user' => [
        'id' => $user->id,
        'username' => $user->username,
        'email' => $user->email,
        'first_name' => $user->first_name ?? '',
        'last_name' => $user->last_name ?? '',
      ],
      'role' => $userRole ? $userRole->role_id : null,
      'expires_at' => $this->sessionExpiresAt(),
    ], 200);
  }

  /**
   * Returns a fresh expires_at without rotating the session ID.
   * Used by the frontend's idle-timeout UI.
   *
   * Laravel's StartSession middleware already updates the underlying session
   * record's last_activity timestamp on every authenticated request, so this
   * endpoint deliberately does no session writes of its own. Rotating the
   * session ID here would race with concurrent SPA navigation and invalidate
   * the cookie out from under in-flight requests.
   */
  public function touchSession(Request $request)
  {
    return response()->json([
      'message' => 'Session refreshed.',
      'expires_at' => $this->sessionExpiresAt(),
    ], 200);
  }

  public function updatePassword(Request $request)
  {
    $fields = $request->validate([
      'current_password' => 'required|string',
      'new_password' => 'required|string|min:8|confirmed',
    ]);

    $user = $request->user();

    // Check if current password is correct
    if (!Hash::check($fields['current_password'], $user->password)) {
      return response()->json([
        'message' => 'Current password is incorrect.',
        'errors' => [
          'current_password' => ['Current password is incorrect.']
        ]
      ], 422);
    }

    // Update password
    $user->password = Hash::make($fields['new_password']);
    $user->save();

    $this->logAuth('Password changed', [
      'event' => 'password.change',
      'user_id' => $user->id,
      'ip' => $request->ip(),
    ]);

    return response()->json([
      'message' => 'Password updated successfully.'
    ], 200);
  }

  private function errorResponse(string $message, array $errors)
  {
    return response()->json([
      'message' => $message,
      'errors' => $errors
    ], 422);
  }

  private function sessionExpiresAt(): string
  {
    return Carbon::now()
      ->addMinutes((int) config('session.lifetime'))
      ->toIso8601String();
  }

  /**
   * Write an audit entry to the dedicated 'auth' log channel.
   * Never include password or password hash in $context.
   */
  private function logAuth(string $message, array $context): void
  {
    Log::channel('auth')->info($message, $context);
  }
}
