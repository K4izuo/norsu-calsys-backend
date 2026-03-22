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
    Schema::create('reservation_statuses', function (Blueprint $table) {
      $table->id();
      $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
      $table->foreignId('moved_by_user')->constrained('users')->onDelete('cascade');
      $table->text('reason');
      $table->date('old_date');
      $table->time('old_time_start');
      $table->time('old_time_end');
      $table->date('new_date');
      $table->time('new_time_start');
      $table->time('new_time_end');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('reservation_statuses');
  }
};
