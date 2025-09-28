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
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade'); // Связь с бронированиями
            $table->date('date_of_birth'); // Дата рождения
            $table->foreignId('passport_id')->constrained()->onDelete('cascade'); // Связь с паспортами
            $table->foreignId('name_id')->constrained()->onDelete('cascade'); // Связь с именами
            $table->foreignId('contact_id')->constrained()->onDelete('cascade'); // Связь с контактами
            $table->timestamps(); // created_at и updated_at

            // Индексы для быстрого поиска
            $table->index('booking_id');
            $table->index('date_of_birth');
            $table->index('passport_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_passengers');
    }
};
