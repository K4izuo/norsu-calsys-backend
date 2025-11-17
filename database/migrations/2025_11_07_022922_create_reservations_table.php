<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('title_name');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->integer('range');
            $table->dateTime('time_start');
            $table->dateTime('time_end');
            $table->string('description');
            $table->string('people_tag');
            $table->string('info_type');
            $table->string('category');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
