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
        Schema::create('privileges', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Название льготы
            $table->text('description')->nullable(); // Описание льготы
            $table->decimal('discount', 5, 2)->default(0); // Скидка в процентах
            $table->timestamps(); // created_at и updated_at

            // Индексы для быстрого поиска
            $table->index('title');
            $table->index('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privileges');
    }
};
