<?php

namespace Database\Seeders;

use App\Models\Roles as ModelsRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class roles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'STUDENT'],
            ['role_name' => 'FACULTY'],
            ['role_name' => 'STAFF'],
            ['role_name' => 'ADMIN'],
            ['role_name' => 'SUPER ADMIN'],
        ];

        foreach ($roles as $role){
            ModelsRoles::firstOrCreate($role);
        }
    }
}
