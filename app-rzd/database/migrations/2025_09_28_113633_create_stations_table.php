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
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Название станции
            $table->text('address'); // Адрес станции
            $table->string('photo_path')->nullable(); // Путь к фото (может быть пустым)
            $table->string('phone')->nullable(); // Телефон станции (может быть пустым)
            $table->timestamps(); // created_at и updated_at

            // Индексы для быстрого поиска
            $table->index('title');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
