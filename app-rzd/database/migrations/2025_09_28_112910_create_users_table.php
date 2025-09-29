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
            $table->foreignIdFor(\App\Models\Role::class, "role_id")->constrained()->onDelete('cascade'); // Связь с roles
            $table->foreignIdFor(\App\Models\Contact::class,'contact_id')->constrained()->onDelete('cascade'); // Связь с contacts
            $table->foreignIdFor(\App\Models\Name::class,'name_id')->constrained()->onDelete('cascade'); // Связь с names

            $table->timestamps(); // created_at и updated_at

            // Индексы для быстрого поиска
            $table->index('login');
            $table->index('role_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
