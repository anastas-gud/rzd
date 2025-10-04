<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CarriageTypeSeeder::class,
            TrainSeeder::class,
            CarriageSeeder::class,
            StationSeeder::class,
            RouteSeeder::class,
            TripSeeder::class,
            SeatSeeder::class,
            ServiceSeeder::class,
            PrivilegeSeeder::class,
        ]);
    }
}
