<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('involves_students')->default(false);
            $table->boolean('requires_vpaa')->default(false);
            $table->boolean('requires_vpsas')->default(false);
            $table->boolean('requires_vpaf')->default(false);
            $table->boolean('requires_vprde')->default(false);
            $table->string('current_stage')->nullable();
            $table->string('declined_at_stage')->nullable();
            $table->string('campus_director_action')->nullable();
        });

        // Backfill existing rows
        DB::table('reservations')->where('status', 'APPROVED')->update(['current_stage' => 'approved']);
        DB::table('reservations')->where('status', 'DECLINED')->update(['current_stage' => 'declined']);
        DB::table('reservations')->where('status', 'PENDING')->update(['current_stage' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'involves_students',
                'requires_vpaa',
                'requires_vpsas',
                'requires_vpaf',
                'requires_vprde',
                'current_stage',
                'declined_at_stage',
                'campus_director_action',
            ]);
        });
    }
};
