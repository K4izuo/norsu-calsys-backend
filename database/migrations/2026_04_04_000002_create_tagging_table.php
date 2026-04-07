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
        Schema::create('taggings', function (Blueprint $table) {
            $table->id('tagID');
            $table->foreignId('tagPeopleID')->constrained('people')->onDelete('cascade');
            $table->foreignId('taggedReservationID')->constrained('reservations')->onDelete('cascade');
            $table->unique(['tagPeopleID', 'taggedReservationID']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taggings');
    }
};
