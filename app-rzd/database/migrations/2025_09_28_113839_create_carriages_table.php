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
        Schema::create('carriages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Train::class,'train_id')->constrained()->onDelete('cascade'); // Связь с поездами
            $table->foreignIdFor(\App\Models\CarriageType::class,'carriage_type_id')->constrained('carriage_types')->onDelete('cascade'); // Связь с типами вагонов
            $table->integer('number'); // Номер вагона в поезде
            $table->timestamps(); // created_at и updated_at

            // Уникальность номера вагона в пределах поезда
            $table->unique(['train_id', 'number']);

            // Индексы
            $table->index('number');
            $table->index(['train_id', 'carriage_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriages');
    }
};
