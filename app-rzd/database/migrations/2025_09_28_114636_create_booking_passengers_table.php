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
            $table->foreignIdFor(\App\Models\Booking::class,'booking_id')->constrained()->onDelete('cascade'); // Связь с бронированиями
            $table->foreignIdFor(\App\Models\Document::class,'document_id')->constrained()->onDelete('cascade'); // Связь с документами
            $table->foreignIdFor(\App\Models\Name::class,'name_id')->constrained()->onDelete('cascade'); // Связь с именами
            $table->foreignIdFor(\App\Models\Contact::class,'contact_id')->constrained()->onDelete('cascade'); // Связь с контактами
            $table->timestamps(); // created_at и updated_at

            // Индексы для быстрого поиска
            $table->index('booking_id');
            $table->index('document_id');
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
