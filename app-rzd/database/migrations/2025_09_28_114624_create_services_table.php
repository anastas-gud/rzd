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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Название услуги
            $table->text('description')->nullable(); // Описание услуги
            $table->decimal('base_price', 10, 2); // Базовая цена
            $table->timestamps(); // created_at и updated_at

            // Индексы для быстрого поиска
            $table->index('title');
            $table->index('base_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
