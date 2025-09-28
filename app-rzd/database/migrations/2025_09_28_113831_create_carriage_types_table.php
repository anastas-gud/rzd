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
        Schema::create('carriage_types', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Название типа вагона
            $table->integer('seats_number'); // Количество мест
            $table->timestamps(); // created_at и updated_at

            // Индексы
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriage_types');
    }
};
