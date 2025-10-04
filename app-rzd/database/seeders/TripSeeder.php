<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\Route;
use App\Models\Train;
use Carbon\Carbon;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $routes = Route::all();
        $trains = Train::all();

        if ($routes->isEmpty() || $trains->isEmpty()) {
            $this->command->warn('Необходимо создать активные маршруты и поезда!');
            return;
        }

        $tripsCreated = 0;
        $usedTrains = [];

        // Создаем поездки на ближайшие 25 дней
        for ($day = 1; $day <= 25; $day++) {
            $date = Carbon::now()->addDays($day);
            $usedTrains[$date->toDateString()] = [];

            foreach ($routes as $route) {
                // Создаем 1-2 поездки в день для каждого маршрута
                $tripsPerDay = rand(1, 2);

                for ($i = 0; $i < $tripsPerDay; $i++) {
                    // Выбираем поезд, который еще не используется в этот день
                    $availableTrains = $trains->whereNotIn('id', $usedTrains[$date->toDateString()]);

                    if ($availableTrains->isEmpty()) {
                        continue;
                    }

                    $train = $availableTrains->random();
                    $usedTrains[$date->toDateString()][] = $train->id;

                    // Генерируем время отправления
                    $startTime = $this->generateUniqueStartTime($date, $route->id, $i);

                    // ПРАВИЛЬНО рассчитываем время окончания
                    $endTime = $this->calculateEndTime($startTime, $route);

                    Trip::firstOrCreate(
                        [
                            'train_id' => $train->id,
                            'route_id' => $route->id,
                            'start_timestamp' => $startTime,
                        ],
                        [
                            'end_timestamp' => $endTime,
                            'is_denied' => false,
                        ]
                    );

                    $tripsCreated++;
                }
            }
        }

        // Создаем дополнительные тестовые поездки
        Trip::factory()->current()->count(3)->create();
        Trip::factory()->completed()->count(5)->create();
        Trip::factory()->denied()->count(2)->create();

        $this->command->info('Поездки созданы успешно!');
        $this->command->info('Всего поездок: ' . $tripsCreated);
        $this->command->info('Предстоящих: ' . Trip::upcoming()->count());
        $this->command->info('Текущих: ' . Trip::current()->count());
        $this->command->info('Завершенных: ' . Trip::completed()->count());
        $this->command->info('Отмененных: ' . Trip::where('is_denied', true)->count());
    }

    /**
     * Генерирует уникальное время отправления для поездки
     */
    private function generateUniqueStartTime(Carbon $date, int $routeId, int $tripIndex): Carbon
    {
        $baseHour = 6 + ($tripIndex * 6);
        $baseHour = min($baseHour, 22);

        $hour = $baseHour + rand(-2, 2);
        $hour = max(6, min($hour, 22));

        $minute = rand(0, 59);

        $minuteVariation = ($routeId % 4) * 15;
        $minute = ($minute + $minuteVariation) % 60;

        return $date->copy()->setTime($hour, $minute, 0);
    }

    /**
     * ПРАВИЛЬНО рассчитывает время окончания поездки
     */
    private function calculateEndTime(Carbon $startTime, Route $route): Carbon
    {
        // Используем продолжительность из маршрута
        $durationMinutes = $route->duration_minutes;

        // Если продолжительность не установлена, используем разумное значение по умолчанию
        if (!$durationMinutes || $durationMinutes <= 0) {
            // Случайная продолжительность от 1 до 8 часов
            $durationMinutes = rand(60, 2880);
        }

        // Убеждаемся, что продолжительность положительная
        $durationMinutes = max(60, $durationMinutes);

        return $startTime->copy()->addMinutes($durationMinutes);
    }

    /**
     * Альтернативный метод с проверкой на пересечение временных интервалов
     */
    private function calculateEndTimeWithValidation(Carbon $startTime, Route $route, Train $train): Carbon
    {
        $durationMinutes = $route->duration_minutes ?: rand(120, 480);
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

        // Проверяем, нет ли у этого поезда других поездок в это время
        $conflictingTrips = Trip::where('train_id', $train->id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_timestamp', [$startTime, $endTime])
                    ->orWhereBetween('end_timestamp', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_timestamp', '<=', $startTime)
                            ->where('end_timestamp', '>=', $endTime);
                    });
            })
            ->exists();

        // Если есть конфликт, сдвигаем время
        if ($conflictingTrips) {
            $startTime->addHours(2); // Сдвигаем на 2 часа
            $endTime = $startTime->copy()->addMinutes($durationMinutes);
        }

        return $endTime;
    }

    private function generateTripNumber(string $routeNumber, int $day, int $index): string
    {
        return $routeNumber . '-' . str_pad($day, 2, '0', STR_PAD_LEFT) . '-' . ($index + 1);
    }
}
