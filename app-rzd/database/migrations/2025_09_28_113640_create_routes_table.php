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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Station::class,'start_station_id')->constrained('stations')->onDelete('cascade'); // Станция отправления
            $table->foreignIdFor(\App\Models\Station::class,'end_station_id')->constrained('stations')->onDelete('cascade'); // Станция назначения
            $table->string('number'); // Номер маршрута
            $table->timestamps(); // created_at и updated_at

            // Уникальность номера маршрута
            $table->unique('number');

            // Индексы для быстрого поиска
            $table->index('number');
            $table->index(['start_station_id', 'end_station_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
