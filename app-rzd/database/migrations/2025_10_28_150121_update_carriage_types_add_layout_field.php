<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carriage_types', function (Blueprint $table) {
            $table->json('layout_json')->after('seats_number');
        });
    }

    public function down(): void
    {
        Schema::table('carriage_types', function (Blueprint $table) {
            $table->dropColumn('layout_json');
        });
    }
};
