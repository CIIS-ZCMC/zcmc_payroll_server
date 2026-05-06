<?php

use App\Http\Controllers\Authentication\AuthenticationController;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Employee\EmployeeAdjustmentController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeeDeductionController;
use App\Http\Controllers\Employee\EmployeePreviewController;
use App\Http\Controllers\Employee\EmployeeReceivableController;
use App\Http\Controllers\Employee\EmployeeTimeRecordController;
use App\Http\Controllers\Employee\ExcludedEmployeeController;
use App\Http\Controllers\Payroll\EmployeePayrollController;
use App\Http\Controllers\Payroll\GeneralPayrollController;
use App\Http\Controllers\Payroll\PayrollPeriodController;
use App\Http\Controllers\Payroll\PayrollProcessController;
use App\Http\Controllers\Payroll\PayrollReportController;
use App\Http\Controllers\Payroll\PayrollSummaryController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Settings\DeductionController;
use App\Http\Controllers\Settings\DeductionGroupController;
use App\Http\Controllers\Settings\DeductionRuleController;
use App\Http\Controllers\Settings\ReceivableController;
use App\Http\Controllers\Trail\EmployeeDeductionTrailController;
use App\Http\Controllers\UMIS\EmployeeProfileController;
use App\Http\Controllers\UMIS\FetchEmployeeController;
use App\Http\Controllers\UMIS\FetchingProgressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Version 2 Api's
Route::post('sign-in', [LoginController::class, 'store']);
Route::post('sign-out', [LoginController::class, 'destroy']);


Route::get('check-connection', [LoginController::class, 'checkServerDatabaseConnection']);
Route::middleware('auth.token')->group(function () {
    //Authentication
    Route::get('authentications', [AuthenticationController::class, 'index']);

    //Libraries
    // Route::put('deduction-stop/{id}', [DeductionController::class, 'stop']);
    Route::apiResource('deductions', DeductionController::class);
    Route::apiResource('deduction-rules', DeductionRuleController::class);
    Route::apiResource('deduction-groups', DeductionGroupController::class);
    Route::apiResource('receivables', ReceivableController::class);

    // UMIS
    // Route::get('fetch_record_step_1', [EmployeeProfileController::class, 'fetchStep1']);
    // Route::post('fetch_record_step_2', [EmployeeProfileController::class, 'fetchStep2']);
    // Route::post('fetch_record_step_3', [EmployeeProfileController::class, 'fetchStep3']);
    // Route::post('fetch_record_step_4', [EmployeeProfileController::class, 'fetchStep4']);

    //Fetch from Redis
    Route::apiResource('fetch-employees', FetchEmployeeController::class)->only(['index', 'store']);
    Route::apiResource('fetch-progress', FetchingProgressController::class)->only(['index']);

    //Employee
    Route::apiResource('employees', EmployeeController::class)->only(['index', 'show']);
    Route::apiResource('employee-deductions', EmployeeDeductionController::class);
    Route::apiResource('employee-receivables', EmployeeReceivableController::class);
    Route::apiResource('employee-time-records', EmployeeTimeRecordController::class);
    Route::apiResource('employee-adjustments', EmployeeAdjustmentController::class)->only(['store', 'show']);
    Route::apiResource('employee-deduction-trail', EmployeeDeductionTrailController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::apiResource('employee-preview', EmployeePreviewController::class)->only(['index', 'show']);
    Route::apiResource('excluded-employees', ExcludedEmployeeController::class)->only(['index', 'store', 'update', 'destroy']);

    //Payroll Period
    Route::apiResource('payroll-process', PayrollProcessController::class)->only(['store', 'show', 'update']);
    Route::apiResource('payroll-periods', PayrollPeriodController::class)->only(['index', 'update']);
    Route::get("payroll-period-lists", [PayrollPeriodController::class, 'payrollPeriodList']);

    //Employee Payroll
    Route::apiResource('employee-payrolls', EmployeePayrollController::class)->only(['index', 'store', 'show']);

    //General  (Not using)
    Route::apiResource('general-payrolls', GeneralPayrollController::class)->only(['index', 'update', 'destroy']);

    //Report
    Route::apiResource('payroll-reports', PayrollReportController::class)->only(['index', 'store']);
    Route::apiResource('payroll-summary', PayrollSummaryController::class)->only(['index', 'store']);
});