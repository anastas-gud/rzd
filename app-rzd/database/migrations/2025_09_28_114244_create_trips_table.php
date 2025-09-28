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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('train_id')->constrained()->onDelete('cascade'); // Связь с поездами
            $table->foreignId('route_id')->constrained()->onDelete('cascade'); // Связь с маршрутами
            $table->timestamp('start_timestamp'); // Время отправления
            $table->timestamp('end_timestamp'); // Время прибытия
            $table->boolean('is_denied')->default(false); // Отменен ли рейс
            $table->timestamps(); // created_at и updated_at

            // Уникальность поезда на маршруте в определенное время
            $table->unique(['train_id', 'route_id', 'start_timestamp']);

            // Индексы для быстрого поиска
            $table->index('start_timestamp');
            $table->index('end_timestamp');
            $table->index('is_denied');
            $table->index(['start_timestamp', 'is_denied']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
