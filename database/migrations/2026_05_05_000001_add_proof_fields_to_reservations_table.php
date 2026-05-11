<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('reservations', function (Blueprint $table) {
      $table->string('proof_of_request', 2000)->nullable()->after('requestor_tagged');
      $table->string('proof_of_approval', 2000)->nullable()->after('proof_of_request');
    });
  }

  public function down(): void
  {
    Schema::table('reservations', function (Blueprint $table) {
      $table->dropColumn(['proof_of_request', 'proof_of_approval']);
    });
  }
};
