<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $fields = $request->validate([
      'username' => 'required|string|max:255',
      'password' => 'required|string',
    ]);

    // Find user by username
    $user = User::where('username', $fields['username'])->first();

    // Check if user exists
    if (!$user) {
      return response()->json([
        'message' => 'Invalid username',
        'errors' => [
          'username' => ['The username does not exist.']
        ]
      ], 422);
    }

    // Check if password is correct
    if (!Hash::check($fields['password'], $user->password)) {
      return response()->json([
        'message' => 'Invalid password',
        'errors' => [
          'password' => ['The password is incorrect.']
        ]
      ], 422);
    }

    // Get the role_id for this user from user_roles table
    $userRole = UserRoles::where('user_id', $user->id)->first();

    if (!$userRole) {
      return response()->json([
        'message' => 'User role not found.',
        'errors' => [
          'username' => ['User role configuration is missing.']
        ]
      ], 422);
    }

    // Create token with expiration
    $token = $user->createToken(
      'auth-token',
      ['*'],
      Carbon::now()->addHours(8) // 8 hours expiration
    )->plainTextToken;

    return response()->json([
      'token' => $token,
      'role' => $userRole->role_id,
      'user' => [
        'id' => $user->id,
        'username' => $user->username,
        'email' => $user->email,
      ],
      'expires_at' => Carbon::now()->addHours(8)->toIso8601String(),
    ], 200);
  }

  public function logout(Request $request)
  {
    // Delete the current access token
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'message' => 'Logged out successfully'
    ], 200);
  }

  public function logoutAll(Request $request)
  {
    // Delete all tokens for the user
    $request->user()->tokens()->delete();

    return response()->json([
      'message' => 'Logged out from all devices successfully'
    ], 200);
  }
}
