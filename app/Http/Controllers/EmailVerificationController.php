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

        $verifyUrl = "https://your-frontend-domain.com/verify-email?token={$token}";

        $htmlBody = "
            <h2>Hello {$user->first_name} 👋</h2>
            <p>Please verify your email by clicking the link below:</p>
            <p><a href='{$verifyUrl}' style='color:#16a34a;font-weight:bold;'>Verify Email</a></p>
        ";

        // Use your PHPMailer service
        // \App\Services\MailService::send(
        //     $user->email,
        //     'Resend Email Verification - NORSU Calendar System',
        //     $htmlBody,
        //     $user->first_name
        // );

        return response()->json(['message' => 'Verification email resent successfully.'], 200);
    }
}
