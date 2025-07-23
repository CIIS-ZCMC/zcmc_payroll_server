<?php

namespace App\Providers;

use App\Contract\EmployeeAdjustmentInterface;
use App\Contract\Repositories\EmployeeAdjustmentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            EmployeeAdjustmentInterface::class,
            EmployeeAdjustmentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
