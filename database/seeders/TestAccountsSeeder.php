<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRoles;
use App\Models\Campuses;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $mainCampus = Campuses::where('campus_acr', 'MAIN')->first();

        if (!$mainCampus) {
            $this->command->error('Run campuses_seeder first: php artisan db:seed --class=campuses_seeder');
            return;
        }

        $accounts = [
            [
                'role_id'     => 4,
                'role_label'  => 'Student Director',
                'first_name'  => 'Maria',
                'middle_name' => 'Cruz',
                'last_name'   => 'Santos',
                'email'       => 'student.director@norsu.edu.ph',
                'username'    => 'student_director',
                'password'    => 'Password123!',
            ],
            [
                'role_id'     => 5,
                'role_label'  => 'Campus Director',
                'first_name'  => 'Jose',
                'middle_name' => 'Reyes',
                'last_name'   => 'Garcia',
                'email'       => 'campus.director@norsu.edu.ph',
                'username'    => 'campus_director',
                'password'    => 'Password123!',
            ],
            [
                'role_id'     => 6,
                'role_label'  => 'VPAA',
                'first_name'  => 'Ana',
                'middle_name' => 'Lopez',
                'last_name'   => 'Mendoza',
                'email'       => 'vpaa@norsu.edu.ph',
                'username'    => 'vpaa_user',
                'password'    => 'Password123!',
            ],
            [
                'role_id'     => 7,
                'role_label'  => 'VPSAS',
                'first_name'  => 'Carlos',
                'middle_name' => 'Dela',
                'last_name'   => 'Rosa',
                'email'       => 'vpsas@norsu.edu.ph',
                'username'    => 'vpsas_user',
                'password'    => 'Password123!',
            ],
            [
                'role_id'     => 8,
                'role_label'  => 'VPAF',
                'first_name'  => 'Elena',
                'middle_name' => 'Bautista',
                'last_name'   => 'Torres',
                'email'       => 'vpaf@norsu.edu.ph',
                'username'    => 'vpaf_user',
                'password'    => 'Password123!',
            ],
            [
                'role_id'     => 9,
                'role_label'  => 'VPRDE',
                'first_name'  => 'Ricardo',
                'middle_name' => 'Navarro',
                'last_name'   => 'Flores',
                'email'       => 'vprde@norsu.edu.ph',
                'username'    => 'vprde_user',
                'password'    => 'Password123!',
            ],
            [
                'role_id'     => 11,
                'role_label'  => 'Multimedia',
                'first_name'  => 'Miguel',
                'middle_name' => 'Santos',
                'last_name'   => 'Reyes',
                'email'       => 'multimedia@norsu.edu.ph',
                'username'    => 'multimedia_user',
                'password'    => 'Password123!',
            ],
            [
                'role_id'     => 12,
                'role_label'  => 'University President',
                'first_name'  => 'Roberto',
                'middle_name' => 'dela',
                'last_name'   => 'Cruz',
                'email'       => 'president@norsu.edu.ph',
                'username'    => 'university_president',
                'password'    => 'Password123!',
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::firstOrCreate(
                ['username' => $account['username']],
                [
                    'first_name'  => $account['first_name'],
                    'middle_name' => $account['middle_name'],
                    'last_name'   => $account['last_name'],
                    'email'       => $account['email'],
                    'password'    => Hash::make($account['password']),
                    'campus_id'   => $mainCampus->id,
                ]
            );

            UserRoles::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'role_id' => $account['role_id'],
                    'full_id' => 0,
                ]
            );

            $this->command->info("✓ {$account['role_label']}: {$account['username']} / {$account['password']}");
        }
    }
}
