<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\Route;
use App\Models\Train;
use Carbon\Carbon;
// TODO исправить start и end, а то они в основном одинаковые
// TODO места и другие тестовые данные
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

        // Создаем поездки на ближайшие 14 дней
        for ($day = 1; $day <= 25; $day++) {
            $date = Carbon::now()->addDays($day);

            foreach ($routes as $route) {
                // Создаем 1-2 поездки в день для каждого маршрута
                $tripsPerDay = rand(1, 2);

                for ($i = 0; $i < $tripsPerDay; $i++) {
                    $train = $trains->random();

                    $startTime = $date->copy()->setTime(rand(6, 22), rand(0, 3) * 15);
                    $endTime = $startTime->copy()->addMinutes($route->duration_minutes);

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

        // Создаем несколько текущих поездок
        Trip::factory()->current()->count(3)->create();

        // Создаем несколько завершенных поездок
        Trip::factory()->completed()->count(5)->create();

        // Создаем несколько отмененных поездок
        Trip::factory()->denied()->count(2)->create();

        $this->command->info('Поездки созданы успешно!');
        $this->command->info('Всего поездок: ' . $tripsCreated);
        $this->command->info('Предстоящих: ' . Trip::upcoming()->count());
        $this->command->info('Текущих: ' . Trip::current()->count());
        $this->command->info('Завершенных: ' . Trip::completed()->count());
        $this->command->info('Отмененных: ' . Trip::where('is_denied', true)->count());
    }

    private function generateTripNumber(string $routeNumber, int $day, int $index): string
    {
        return $routeNumber . '-' . str_pad($day, 2, '0', STR_PAD_LEFT) . '-' . ($index + 1);
    }
}
