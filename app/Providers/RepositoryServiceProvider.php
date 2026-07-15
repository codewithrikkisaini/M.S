<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ReservationRepositoryInterface;
use App\Repositories\ReservationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
