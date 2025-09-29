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
        Schema::create('names', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Имя
            $table->string('surname'); // Фамилия
            $table->string('patronymic')->nullable(); // Отчество (может быть пустым)
            $table->timestamps(); // created_at и updated_at

            // Индекс для поиска по ФИО
            $table->index(['surname', 'name', 'patronymic']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('names');
    }
};
