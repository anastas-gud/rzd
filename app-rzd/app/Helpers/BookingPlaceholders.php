<?php
namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingPlaceholders
{
    public static function name(): array
    {
        $now = Carbon::now();
        return [
            'serial' => 'TBD-' . strtoupper(Str::random(4)),
            'number' => 'TBD-' . strtoupper(Str::random(6)),
            'name' => "null",
            'surname' => "null",
            'patronymic' => "null",
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    public static function document(): array
    {
        $now = Carbon::now();
        return [
            'date_of_birth' => '1900-01-01',
            'serial' => 'TBD-' . strtoupper(Str::random(4)),
            'number' => 'TBD-' . strtoupper(Str::random(6)),
            'type_of_document' => 'PASSPORT',
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    public static function contact(): array
    {
        $now = Carbon::now();
        return [
            'phone' => null,
            'email' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
