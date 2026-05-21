<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $fields = $request->validate([
      'username' => 'required|string|max:255',
      'password' => 'required|string',
    ]);

    $user = User::where('username', $fields['username'])->first();

    // Username doesn't exist
    if (!$user) {
      return $this->errorResponse('The provided credentials are incorrect.', [
        'username' => ['The provided credentials are incorrect.'],
        'password' => ['The provided credentials are incorrect.']
      ]);
    }

    // Password is incorrect
    if (!Hash::check($fields['password'], $user->password)) {
      return $this->errorResponse('Password is incorrect.', [
        'password' => ['Password is incorrect.']
      ]);
    }

    // Get user role
    $userRole = UserRoles::where('user_id', $user->id)->first();

    if (!$userRole) {
      return $this->errorResponse('User role not found.', [
        'username' => ['User role configuration is missing.']
      ]);
    }

    // Establish a session (SPA cookie auth) and rotate the session ID
    // to defend against session fixation.
    Auth::guard('web')->login($user);
    $request->session()->regenerate();

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
    Auth::guard('web')->logout();

    // Only touch the session when one is actually bound to the request
    // (stateful SPA call). Non-stateful callers — e.g. a one-off API
    // probe — should not crash logout just because there is no session.
    if ($request->hasSession()) {
      $request->session()->invalidate();
      $request->session()->regenerateToken();
    }

    return response()->json(['message' => 'Logged out successfully'], 200);
  }

  public function logoutAll(Request $request)
  {
    // SPA mode: a single browser holds one session; treat this as a regular logout.
    Auth::guard('web')->logout();

    if ($request->hasSession()) {
      $request->session()->invalidate();
      $request->session()->regenerateToken();
    }

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
   * Touch the current session: rotates the session ID and reports a fresh expiry.
   *
   * Deprecated alias for the legacy /update-token-expiration endpoint, which
   * existed to refresh a 15-minute personal access token. SPA sessions
   * already roll forward automatically on each authenticated request; this
   * endpoint stays as a no-op session touch so the frontend's idle-timeout
   * UI keeps working during the cutover window.
   */
  public function updateTokenExpiration(Request $request)
  {
    $request->session()->migrate(true);

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
}
