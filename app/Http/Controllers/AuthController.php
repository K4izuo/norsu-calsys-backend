<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    private const TOKEN_EXPIRY_HOURS = 8;

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

        // Create token
        $expiresAt = Carbon::now()->addHours(self::TOKEN_EXPIRY_HOURS);
        $token = $user->createToken('auth-token', ['*'], $expiresAt)->plainTextToken;

        return response()->json([
            'token' => $token,
            'role' => $userRole->role_id,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
            ],
            'expires_at' => $expiresAt->toIso8601String(),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();
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
        ], 200);
    }

    private function errorResponse(string $message, array $errors)
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors
        ], 422);
    }
}