<?php

namespace App\Providers;

use App\Interfaces\DashboardRepositoryInterface;
use App\Interfaces\PatientRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\DashboardRepository;
use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PatientRepositoryInterface::class, PatientRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
