<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Step 1: Move existing user_roles to temp values to avoid conflicts
        DB::table('user_roles')->where('role_id', 2)->update(['role_id' => 11]); // dean temp
        DB::table('user_roles')->where('role_id', 3)->update(['role_id' => 22]); // staff temp
        DB::table('user_roles')->where('role_id', 4)->update(['role_id' => 33]); // admin temp

        // Step 2: Set final role_ids
        DB::table('user_roles')->where('role_id', 11)->update(['role_id' => 1]); // dean → 1
        DB::table('user_roles')->where('role_id', 22)->update(['role_id' => 2]); // staff → 2
        DB::table('user_roles')->where('role_id', 33)->update(['role_id' => 3]); // admin → 3

        // Step 3: Rename roles table entries
        DB::table('roles')->where('id', 1)->update(['role_name' => 'FACULTY']); // was STUDENT
        DB::table('roles')->where('id', 2)->update(['role_name' => 'STAFF']);   // was FACULTY
        DB::table('roles')->where('id', 3)->update(['role_name' => 'ADMIN']);   // was STAFF

        // Step 4: Remove obsolete roles (old ADMIN=4, old SUPER ADMIN=5)
        DB::table('roles')->whereIn('id', [4, 5])->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Restore roles table
        DB::table('roles')->where('id', 1)->update(['role_name' => 'STUDENT']);
        DB::table('roles')->where('id', 2)->update(['role_name' => 'FACULTY']);
        DB::table('roles')->where('id', 3)->update(['role_name' => 'STAFF']);
        DB::table('roles')->insert([
            ['id' => 4, 'role_name' => 'ADMIN'],
            ['id' => 5, 'role_name' => 'SUPER ADMIN'],
        ]);

        // Reverse user_roles remapping
        DB::table('user_roles')->where('role_id', 1)->update(['role_id' => 11]);
        DB::table('user_roles')->where('role_id', 2)->update(['role_id' => 22]);
        DB::table('user_roles')->where('role_id', 3)->update(['role_id' => 33]);
        DB::table('user_roles')->where('role_id', 11)->update(['role_id' => 2]);
        DB::table('user_roles')->where('role_id', 22)->update(['role_id' => 3]);
        DB::table('user_roles')->where('role_id', 33)->update(['role_id' => 4]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
