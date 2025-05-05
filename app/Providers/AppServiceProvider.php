<?php

namespace App\Providers;

use App\Mars\Domain\Repositories\MapRepository;
use App\Mars\Domain\Repositories\RoverRepository;
use App\Mars\Infrastructure\Repositories\InMemoryMapRepository;
use App\Mars\Infrastructure\Repositories\InMemoryRoverRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MapRepository::class, InMemoryMapRepository::class);
        $this->app->bind(RoverRepository::class, InMemoryRoverRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
