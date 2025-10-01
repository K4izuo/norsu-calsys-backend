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
        Schema::create('degree_courses', function (Blueprint $table) {
            $table->id();
            // $table->integer('office_id');
            $table->string('degree_name');
            $table->string('degree_acr');
            $table->foreignId('office_id')->constrained("offices")->onDelete('cascade');
            $table->integer('degree_inp_usr_no');
            $table->timestamp('degree_inp_timestamp');
            $table->integer('degree_upd_usr_no');
            $table->timestamp('degree_upd_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('degree_courses');
    }
};
