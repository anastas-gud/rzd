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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('login')->unique(); // Логин (уникальный)
            $table->string('password'); // Пароль

            // Внешние ключи
            $table->foreignId('role_id')->constrained()->onDelete('cascade'); // Связь с roles
            $table->foreignId('contact_id')->constrained()->onDelete('cascade'); // Связь с contacts
            $table->foreignId('name_id')->constrained()->onDelete('cascade'); // Связь с names

            $table->timestamps(); // created_at и updated_at

            // Индексы для быстрого поиска
            $table->index('login');
            $table->index('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
