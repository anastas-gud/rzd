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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class,'user_id')->constrained()->onDelete('cascade'); // Связь с пользователями
            $table->foreignIdFor(\App\Models\Trip::class,'trip_id')->constrained()->onDelete('cascade');
            $table->decimal('final_price', 10, 2); // Итоговая цена
            $table->foreignIdFor(\App\Models\Seat::class,'seat_id')->constrained()->onDelete('cascade'); // Связь с местами
            $table->foreignIdFor(\App\Models\BookingPassenger::class,'booking_passenger_id')->constrained('booking_passengers')->onDelete('cascade'); // Связь с пассажирами
            $table->string('ticket_code')->unique(); // Уникальный код билета
            $table->boolean('is_canceled')->default(false); // Отменен ли билет
            $table->timestamps(); // created_at и updated_at

            // Индексы для быстрого поиска
            $table->index('ticket_code');
            $table->index('is_canceled');
            $table->index(['user_id', 'is_canceled']);
            $table->index('final_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
