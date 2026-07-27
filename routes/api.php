<?php

use App\Http\Controllers\Libraries\DeductionController;
use App\Http\Controllers\Libraries\DeductionGroupController;
use App\Http\Controllers\Libraries\LateDeductionMatrixController;
use App\Http\Controllers\Libraries\NightDifferentialRuleController;
use App\Http\Controllers\Libraries\ReceivableController;
use App\Http\Controllers\Libraries\ReceivableGroupController;

use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeeDeductionController;
use App\Http\Controllers\Employee\EmployeeReceivableController;

use App\Http\Controllers\Api\BulkEmployeeDeductionController;
use App\Http\Controllers\Api\EmployeePayrollController;
use App\Http\Controllers\Api\EmployeeSalaryController;
use App\Http\Controllers\Api\EmployeeTimeRecordController;
use App\Http\Controllers\Api\PayrollPeriodController;
use App\Http\Controllers\Api\PayrollRunController;
use App\Http\Controllers\Api\PayrollSummaryController;
use App\Http\Controllers\Api\PayrollWorkflowController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    /**
     * ===================
     * LIBRARIES
     * ===================
     */
    Route::apiResource('deduction-groups', DeductionGroupController::class);
    Route::apiResource('deductions', DeductionController::class);

    Route::apiResource('receivable-groups', ReceivableGroupController::class);
    Route::apiResource('receivables', ReceivableController::class);

    Route::apiResource('late-deductions-matrix', LateDeductionMatrixController::class);
    Route::apiResource('night-differential-rules', NightDifferentialRuleController::class)->only(['index', 'store', 'show', 'update']);

    /**
     * ===================
     * EMPLOYEES
     * ===================
     */
    Route::apiResource('employees', EmployeeController::class)->only(['index', 'show']);
    Route::apiResource('employee-deductions', EmployeeDeductionController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::apiResource('employee-receivables', EmployeeReceivableController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    // Payroll periods
    Route::get('payroll-periods/active', [PayrollPeriodController::class, 'active'])->name('payroll-periods.active');
    Route::post('payroll-periods/{payroll_period}/activate', [PayrollPeriodController::class, 'activate'])->name('payroll-periods.activate');
    Route::post('payroll-periods/{payroll_period}/lock', [PayrollPeriodController::class, 'lock'])->name('payroll-periods.lock');
    Route::apiResource('payroll-periods', PayrollPeriodController::class)->except(['destroy']);

    // Payroll runs
    Route::post('payroll-runs/{payroll_run}/complete', [PayrollRunController::class, 'complete'])->name('payroll-runs.complete');
    Route::post('payroll-runs/{payroll_run}/lock', [PayrollRunController::class, 'lock'])->name('payroll-runs.lock');
    Route::post('payroll-runs/{payroll_run}/reverse', [PayrollRunController::class, 'reverse'])->name('payroll-runs.reverse');
    Route::apiResource('payroll-runs', PayrollRunController::class)->only(['index', 'store', 'show']);

    // Employee payrolls
    Route::apiResource('employee-payrolls', EmployeePayrollController::class)->except(['destroy']);

    // Payroll summaries
    Route::get('payroll-summaries/period/{payrollPeriodId}', [PayrollSummaryController::class, 'byPeriod'])->name('payroll-summaries.by-period');
    Route::apiResource('payroll-summaries', PayrollSummaryController::class)->only(['index', 'show']);

    // Bulk employee deductions (file import)
    Route::post('bulk-employee-deductions/preview', [BulkEmployeeDeductionController::class, 'preview'])->name('bulk-employee-deductions.preview');
    Route::post('bulk-employee-deductions', [BulkEmployeeDeductionController::class, 'store'])->name('bulk-employee-deductions.store');


    // Employee salaries
    Route::post('employee-salaries/import', [EmployeeSalaryController::class, 'import'])->name('employee-salaries.import');
    Route::apiResource('employee-salaries', EmployeeSalaryController::class)->only(['store', 'update']);

    // Employee time records
    Route::post('employee-time-records/{employee_time_record}/include', [EmployeeTimeRecordController::class, 'include'])->name('employee-time-records.include');
    Route::post('employee-time-records/{employee_time_record}/exclude', [EmployeeTimeRecordController::class, 'exclude'])->name('employee-time-records.exclude');
    Route::apiResource('employee-time-records', EmployeeTimeRecordController::class)->except(['destroy']);
});
