<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class EmailVerificationController extends Controller
{
    /**
     * Verify the user's email based on token.
     */
    public function verify(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return response()->json(['message' => 'Token is required.'], 400);
        }

        $record = DB::table('email_verifications')->where('token', $token)->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }

        $user = User::find($record->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        // Mark user as verified
        $user->email_verified_at = now();
        $user->save();

        // Remove the token after verification
        DB::table('email_verifications')->where('token', $token)->delete();

        return response()->json(['message' => 'Email verified successfully!'], 200);
    }

    /**
     * Resend a new verification link.
     */
    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        // Generate new token
        $token = \Illuminate\Support\Str::random(64);

        // Delete old tokens (optional cleanup)
        DB::table('email_verifications')->where('user_id', $user->id)->delete();

        DB::table('email_verifications')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => now(),
        ]);

        $verifyUrl = rtrim(config('app.frontend_url'), '/') . "/verify-email?token={$token}";

        // TODO: send $verifyUrl via MailService once implemented

        return response()->json(['message' => 'Verification email resent successfully.'], 200);
    }
}
