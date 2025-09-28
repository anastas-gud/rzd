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
        Schema::create('passports', function (Blueprint $table) {
            $table->id();
            $table->string('serial'); // Серия паспорта
            $table->string('number'); // Номер паспорта
            $table->timestamps(); // created_at и updated_at

            // Уникальность паспорта
            $table->unique(['serial', 'number']);

            // Индексы для быстрого поиска
            $table->index(['serial', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passports');
    }
};
