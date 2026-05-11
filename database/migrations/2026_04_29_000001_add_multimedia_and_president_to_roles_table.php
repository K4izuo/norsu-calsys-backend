<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->insert([
            ['id' => 11, 'role_name' => 'multimedia',          'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'role_name' => 'university_president', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('id', [11, 12])->delete();
    }
};
