<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\Station;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $stations = Station::all();

        if ($stations->count() < 2) {
            $this->command->warn('Необходимо создать как минимум 2 станции!');
            return;
        }

        $popularRoutes = [
            [
                'start_city' => 'Москва',
                'end_city' => 'Санкт-Петербург',
                'number' => '001А',
            ],
            [
                'start_city' => 'Москва',
                'end_city' => 'Нижний Новгород',
                'number' => '002Б',
            ],
            [
                'start_city' => 'Москва',
                'end_city' => 'Екатеринбург',
                'number' => '003В',
            ],
            [
                'start_city' => 'Санкт-Петербург',
                'end_city' => 'Екатеринбург',
                'number' => '004Г',
            ],
            [
                'start_city' => 'Москва',
                'end_city' => 'Новосибирск',
                'number' => '005Д',
            ],
        ];

        foreach ($popularRoutes as $routeData) {
            $startStation = Station::where('city', $routeData['start_city'])->first();
            $endStation = Station::where('city', $routeData['end_city'])->first();

            if ($startStation && $endStation) {
                Route::firstOrCreate(
                    [
                        'start_station_id' => $startStation->id,
                        'end_station_id' => $endStation->id,
                        'number' => $routeData['number'],
                    ]
                );
            }
        }

        // Создаем дополнительные случайные маршруты
        Route::factory()->count(10)->create();

        $this->command->info('Маршруты созданы успешно!');
        $this->command->info('Всего маршрутов: ' . Route::count());
    }
}
