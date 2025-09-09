<?php

namespace App\Providers;

use App\Contract\DeductionGroupInterface;
use App\Contract\DeductionInterface;
use App\Contract\DeductionRuleInterface;
use App\Contract\EmployeeAdjustmentInterface;
use App\Contract\EmployeeDeductionInterface;
use App\Contract\EmployeeDeductionTrailInterface;
use App\Contract\EmployeeInterface;
use App\Contract\EmployeePayrollInterface;
use App\Contract\EmployeeReceivableInterface;
use App\Contract\EmployeeReceivableTrailInterface;
use App\Contract\EmployeeSalaryInterface;
use App\Contract\EmployeeTimeRecordInterface;
use App\Contract\ExcludedEmployeeInterface;
use App\Contract\GeneralPayrollInterface;
use App\Contract\PayrollPeriodInterface;
use App\Contract\ReceivableInterface;
use App\Contract\Repositories\DeductionGroupRepository;
use App\Contract\Repositories\DeductionRepository;
use App\Contract\Repositories\DeductionRuleRepository;
use App\Contract\Repositories\EmployeeAdjustmentRepository;
use App\Contract\Repositories\EmployeeDeductionRepository;
use App\Contract\Repositories\EmployeeDeductionTrailRepository;
use App\Contract\Repositories\EmployeePayrollRepository;
use App\Contract\Repositories\EmployeeReceivableRepository;
use App\Contract\Repositories\EmployeeReceivableTrailRepository;
use App\Contract\Repositories\EmployeeRepository;
use App\Contract\Repositories\EmployeeSalaryRepository;
use App\Contract\Repositories\EmployeeTimeRecordRepository;
use App\Contract\Repositories\ExcludedEmployeeRepository;
use App\Contract\Repositories\GeneralPayrollRepository;
use App\Contract\Repositories\PayrollPeriodRepository;
use App\Contract\Repositories\ReceivableRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            DeductionGroupInterface::class,
            DeductionGroupRepository::class,
        );

        $this->app->bind(
            DeductionInterface::class,
            DeductionRepository::class
        );

        $this->app->bind(
            DeductionRuleInterface::class,
            DeductionRuleRepository::class
        );

        $this->app->bind(
            EmployeeAdjustmentInterface::class,
            EmployeeAdjustmentRepository::class
        );

        $this->app->bind(
            EmployeeDeductionInterface::class,
            EmployeeDeductionRepository::class
        );

        $this->app->bind(
            EmployeeDeductionTrailInterface::class,
            EmployeeDeductionTrailRepository::class
        );

        $this->app->bind(
            EmployeeInterface::class,
            EmployeeRepository::class
        );

        $this->app->bind(
            EmployeePayrollInterface::class,
            EmployeePayrollRepository::class
        );

        $this->app->bind(
            EmployeeReceivableInterface::class,
            EmployeeReceivableRepository::class
        );

        $this->app->bind(
            EmployeeReceivableTrailInterface::class,
            EmployeeReceivableTrailRepository::class
        );

        $this->app->bind(
            EmployeeSalaryInterface::class,
            EmployeeSalaryRepository::class
        );

        $this->app->bind(
            EmployeeTimeRecordInterface::class,
            EmployeeTimeRecordRepository::class
        );

        $this->app->bind(
            ExcludedEmployeeInterface::class,
            ExcludedEmployeeRepository::class
        );

        $this->app->bind(
            GeneralPayrollInterface::class,
            GeneralPayrollRepository::class
        );

        $this->app->bind(
            PayrollPeriodInterface::class,
            PayrollPeriodRepository::class
        );

        $this->app->bind(
            ReceivableInterface::class,
            ReceivableRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
