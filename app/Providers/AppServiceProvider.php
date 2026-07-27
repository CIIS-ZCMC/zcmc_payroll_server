<?php

namespace App\Providers;

use App\Contract\DeductionGroupInterface;
use App\Contract\DeductionInterface;
use App\Contract\EmployeeComputedSalaryInterface;
use App\Contract\EmployeeDeductionInterface;
use App\Contract\EmployeeDeductionPaymentInterface;
use App\Contract\EmployeeDeductionTermInterface;
use App\Contract\EmployeeInterface;
use App\Contract\EmployeePayrollInterface;
use App\Contract\EmployeeReceivableInterface;
use App\Contract\EmployeeReceivablePaymentInterface;
use App\Contract\EmployeeReceivableTermInterface;
use App\Contract\EmployeeSalaryInterface;
use App\Contract\EmployeeTimeRecordInterface;
use App\Contract\NightDifferentialRuleInterface;
use App\Contract\PayrollPeriodInterface;
use App\Contract\PayrollProcessInterface;
use App\Contract\PayrollRunInterface;
use App\Contract\PayrollSummaryInterface;
use App\Contract\ReceivableGroupInterface;
use App\Contract\ReceivableInterface;
use App\Repositories\DeductionGroupRepository;
use App\Repositories\DeductionRepository;
use App\Repositories\EmployeeComputedSalaryRepository;
use App\Repositories\EmployeeDeductionPaymentRepository;
use App\Repositories\EmployeeDeductionRepository;
use App\Repositories\EmployeeDeductionTermRepository;
use App\Repositories\EmployeePayrollRepository;
use App\Repositories\EmployeeReceivablePaymentRepository;
use App\Repositories\EmployeeReceivableRepository;
use App\Repositories\EmployeeReceivableTermRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\EmployeeSalaryRepository;
use App\Repositories\EmployeeTimeRecordRepository;
use App\Repositories\NightDifferentialRuleRepository;
use App\Repositories\PayrollPeriodRepository;
use App\Repositories\PayrollProcessRepository;
use App\Repositories\PayrollRunRepository;
use App\Repositories\PayrollSummaryRepository;
use App\Repositories\ReceivableGroupRepository;
use App\Repositories\ReceivableRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Repository contract bindings.
     *
     * @var array<class-string, class-string>
     */
    private array $bindings_map = [
        DeductionGroupInterface::class => DeductionGroupRepository::class,
        ReceivableGroupInterface::class => ReceivableGroupRepository::class,
        DeductionInterface::class => DeductionRepository::class,
        ReceivableInterface::class => ReceivableRepository::class,
        EmployeeInterface::class => EmployeeRepository::class,
        PayrollPeriodInterface::class => PayrollPeriodRepository::class,
        PayrollProcessInterface::class => PayrollProcessRepository::class,
        PayrollRunInterface::class => PayrollRunRepository::class,
        PayrollSummaryInterface::class => PayrollSummaryRepository::class,
        EmployeeSalaryInterface::class => EmployeeSalaryRepository::class,
        EmployeeTimeRecordInterface::class => EmployeeTimeRecordRepository::class,
        EmployeeComputedSalaryInterface::class => EmployeeComputedSalaryRepository::class,
        EmployeeDeductionInterface::class => EmployeeDeductionRepository::class,
        EmployeeDeductionTermInterface::class => EmployeeDeductionTermRepository::class,
        EmployeeDeductionPaymentInterface::class => EmployeeDeductionPaymentRepository::class,
        EmployeeReceivableInterface::class => EmployeeReceivableRepository::class,
        EmployeeReceivableTermInterface::class => EmployeeReceivableTermRepository::class,
        EmployeeReceivablePaymentInterface::class => EmployeeReceivablePaymentRepository::class,
        EmployeePayrollInterface::class => EmployeePayrollRepository::class,
        NightDifferentialRuleInterface::class => NightDifferentialRuleRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->bindings_map as $contract => $repository) {
            $this->app->bind($contract, $repository);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
