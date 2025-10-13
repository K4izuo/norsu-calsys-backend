<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
use App\Services\MailService;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Detect user type from request
        $isStudent = $request->has('studentID');
        // $isFaculty = !$isStudent;

        // Base validation
        $rules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'campus_id' => 'required|integer|exists:campuses,id',
            'degree_course_id' => 'nullable|integer|exists:degree_courses,id',
        ];

        // Specific rules
        if ($isStudent) {
            $rules['studentID'] = 'required|string|max:50|unique:user_roles,full_id';
        } else {
            $rules['facultyID'] = 'required|string|max:50|unique:user_roles,full_id';
        }

        $validated = $request->validate($rules);

        // --- START TRANSACTION ---
        $user = DB::transaction(function () use ($validated, $isStudent) {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'campus_id'  => $validated['campus_id'],
                'degree_course_id' => $validated['degree_course_id'] ?? null,
            ]);

            $roleId = $isStudent ? 1 : 2;
            $fullId = $isStudent ? $validated['studentID'] : $validated['facultyID'];

            UserRoles::create([
                'user_id' => $user->id,
                'role_id' => $roleId,
                'full_id' => $fullId,
            ]);

            return $user;
        });
        // --- END TRANSACTION ---

        // Create verification token
        $token = Str::random(64);
        DB::table('email_verifications')->insert([
            'user_id'    => $user->id,
            'token'      => $token,
            'created_at' => now(),
        ]);

        // Construct verification link (your frontend URL)
        // $verifyUrl = "https://your-frontend-domain.com/verify-email?token={$token}";
        $frontend = rtrim(env('FRONTEND_URL', 'http://192.168.0.16:3000'), '/');
        $verifyUrl = "{$frontend}/auth/student/account?token={$token}";

        // Email HTML content
        $emailBody = "
            <h2>Hello {$user->first_name} 👋</h2>
            <p>Thank you for registering at <strong>NORSU Calendar System</strong>.</p>
            <p>Please verify your email by clicking the link below:</p>
            <p><a href='{$verifyUrl}' style='color:#16a34a;font-weight:bold;'>Verify Email</a></p>
            <p>If you didn’t register, you can safely ignore this message.</p>
        ";

        // Send email using PHPMailer
        $emailSent = MailService::send(
            $user->email,
            'Verify Your Email - NORSU Calendar System',
            $emailBody,
            $user->first_name
        );

        if (!$emailSent) {
            return response()->json([
                'message' => 'User registered but email failed to send.',
            ], 500);
        }

        return response()->json([
            'message' => $isStudent
                ? 'Student registration successful! Verification email sent.'
                : 'Faculty registration successful! Verification email sent.',
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
