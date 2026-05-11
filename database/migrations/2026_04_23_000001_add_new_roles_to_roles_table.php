<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->insert([
            ['id' => 4,  'role_name' => 'STUDENT_DIRECTOR', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5,  'role_name' => 'CAMPUS_DIRECTOR',  'created_at' => now(), 'updated_at' => now()],
            ['id' => 6,  'role_name' => 'VPAA',             'created_at' => now(), 'updated_at' => now()],
            ['id' => 7,  'role_name' => 'VPSAS',            'created_at' => now(), 'updated_at' => now()],
            ['id' => 8,  'role_name' => 'VPAF',             'created_at' => now(), 'updated_at' => now()],
            ['id' => 9,  'role_name' => 'VPRDE',            'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'role_name' => 'HEAD_OF_OFFICE',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('id', [4, 5, 6, 7, 8, 9, 10])->delete();
    }
};
