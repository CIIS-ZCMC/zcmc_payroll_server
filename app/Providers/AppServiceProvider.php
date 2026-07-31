<?php

namespace App\Providers;

use App\Contract\EmployeeAdjustmentInterface;
use App\Contract\Repositories\EmployeeAdjustmentRepository;
use Filament\Facades\Filament;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Without this Filament orders the sidebar headings by whichever
        // resource it happened to discover first, which is effectively random.
        // Listing them fixes the order; $navigationSort on each resource then
        // orders the items inside a heading.
        Filament::serving(function () {
            Filament::registerNavigationGroups([
                'Employees',
                'Manages',
                'Time Records',
                'Payroll',
                'Libraries',
                'Imports',
                'Logs and Trails',
            ]);
        });
    }
}
