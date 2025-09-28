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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carriage_id')->constrained()->onDelete('cascade'); // Связь с вагонами
            $table->integer('number'); // Номер места
            $table->decimal('price', 10, 2); // Цена места
            $table->timestamps(); // created_at и updated_at

            // Уникальность номера места в пределах вагона
            $table->unique(['carriage_id', 'number']);

            // Индексы для быстрого поиска
            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
