<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, make sure all existing users have a value in degree_course_id
        // You can set a default or pick a valid ID
        DB::table('users')->whereNull('degree_course_id')->update(['degree_course_id' => 1]);

        // Then modify the column to be non-nullable
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('degree_course_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('degree_course_id')->nullable()->change();
        });
    }
};
