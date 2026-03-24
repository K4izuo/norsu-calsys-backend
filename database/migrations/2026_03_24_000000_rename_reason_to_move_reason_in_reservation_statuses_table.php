<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_statuses', function (Blueprint $table) {
            $table->renameColumn('reason', 'move_reason');
        });
    }

    public function down(): void
    {
        Schema::table('reservation_statuses', function (Blueprint $table) {
            $table->renameColumn('move_reason', 'reason');
        });
    }
};
