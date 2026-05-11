<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('requestor_type')->nullable();
            $table->string('student_sub_type')->nullable();
            $table->string('student_org_name')->nullable();
            $table->string('csg_name')->nullable();
            $table->json('requestor_tagged')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'requestor_type',
                'student_sub_type',
                'student_org_name',
                'csg_name',
                'requestor_tagged',
            ]);
        });
    }
};
