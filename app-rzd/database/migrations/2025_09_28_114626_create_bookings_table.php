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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class,'user_id')->constrained()->onDelete('cascade'); // Связь с пользователями
            $table->enum('status', ['BOOKED', 'PAID', 'CANCELLED'])->default('BOOKED'); // Статус бронирования
            $table->timestamp('expires_at')->nullable(); // Когда истекает бронь
            $table->decimal('total_price', 10, 2); // Общая стоимость
            $table->timestamps(); // created_at и updated_at

            // Индексы для быстрого поиска
            $table->index('status');
            $table->index('expires_at');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
