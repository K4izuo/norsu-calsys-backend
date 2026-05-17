<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->index('current_stage');
            $table->index('reserved_by_user');
            $table->index(['asset_id', 'date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['current_stage']);
            $table->dropIndex(['reserved_by_user']);
            $table->dropIndex(['asset_id', 'date', 'status']);
        });
    }
};
