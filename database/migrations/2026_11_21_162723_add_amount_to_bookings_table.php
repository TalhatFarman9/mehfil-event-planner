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
    Schema::table('bookings', function (Blueprint $table) {
        $table->decimal('amount', 10, 2)->default(0)->after('status');
        $table->index('event_date'); // grouping fast karne ke liye
    });
}

public function down(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->dropIndex(['event_date']);
        $table->dropColumn('amount');
    });
}
};
