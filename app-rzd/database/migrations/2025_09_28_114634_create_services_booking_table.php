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
        Schema::create('services_booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade'); // Связь с услугами
            $table->foreignId('booking_id')->constrained()->onDelete('cascade'); // Связь с бронированиями
            $table->integer('count')->default(1); // Количество услуги
            $table->decimal('current_price', 10, 2); // Текущая цена на момент бронирования
            $table->timestamps(); // created_at и updated_at

            // Уникальность услуги в пределах бронирования
            $table->unique(['service_id', 'booking_id']);

            // Индексы для быстрого поиска
            $table->index('service_id');
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_booking');
    }
};
