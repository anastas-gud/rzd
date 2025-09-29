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
        Schema::create('booking_passenger_privileges', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Privilege::class,'privilege_id')->constrained('privileges')->onDelete('cascade'); // Связь с льготами
            $table->foreignIdFor(\App\Models\BookingPassenger::class,'booking_passenger_id')->constrained('booking_passengers')->onDelete('cascade'); // Связь с пассажирами
            $table->timestamps(); // created_at и updated_at

            // Уникальность льготы для пассажира
            $table->unique(['privilege_id', 'booking_passenger_id']);

            // Индексы для быстрого поиска
            $table->index('privilege_id');
            $table->index('booking_passenger_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_passenger_privileges');
    }
};
