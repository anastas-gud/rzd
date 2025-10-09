<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TripService;
use App\Services\BookingService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TripService::class, function($app){
            return new TripService();
        });

        $this->app->singleton(BookingService::class, function($app){
            return new BookingService();
        });
    }

    public function boot()
    {
        //
    }
}
