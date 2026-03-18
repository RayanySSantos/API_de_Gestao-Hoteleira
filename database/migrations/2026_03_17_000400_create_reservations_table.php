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
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('rate_id')->nullable()->constrained('rates')->nullOnDelete();
            $table->unsignedBigInteger('room_reservation_id')->nullable()->unique();
            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->date('reservation_date');
            $table->time('reservation_time')->nullable();
            $table->date('check_in');
            $table->date('check_out');
            $table->string('currency_code', 3)->default('BRL');
            $table->string('meal_plan')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['room_id', 'check_in', 'check_out']);
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
