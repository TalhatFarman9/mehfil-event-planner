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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->string('client_name');
        $table->string('client_email');
        $table->string('client_phone', 30)->nullable();
        $table->string('event_type');
        $table->date('event_date');
        $table->unsignedInteger('guests')->default(1);
        $table->string('status')->default('pending');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('bookings');
}
};
