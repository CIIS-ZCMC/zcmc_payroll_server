<?php

use App\Enums\PayrollStep;
use App\Http\Controllers\Authentication\AuthenticationController;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Employee\EmployeeAdjustmentController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeeDeductionController;
use App\Http\Controllers\Employee\EmployeePreviewController;
use App\Http\Controllers\Employee\EmployeeReceivableController;
use App\Http\Controllers\Employee\EmployeeTimeRecordController;
use App\Http\Controllers\Employee\ExcludedEmployeeController;
use App\Http\Controllers\NightDifferential\NightDifferentialRuleController;
use App\Http\Controllers\Payroll\EmployeePayrollController;
use App\Http\Controllers\Payroll\PayrollGenerationController;
use App\Http\Controllers\Payroll\PayrollPeriodController;
use App\Http\Controllers\Payroll\PayrollProcessController;
use App\Http\Controllers\Payroll\PayrollReportController;
use App\Http\Controllers\Payroll\PayrollSelectionController;
use App\Http\Controllers\Payroll\PayrollSummaryController;
use App\Http\Controllers\Settings\DeductionController;
use App\Http\Controllers\Settings\DeductionGroupController;
use App\Http\Controllers\Settings\DeductionRuleController;
use App\Http\Controllers\Settings\ReceivableController;
use App\Http\Controllers\Trail\EmployeeDeductionTrailController;
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

    //Fetch from Redis
    Route::apiResource('fetch-employees', FetchEmployeeController::class)->only(['index', 'store']);
    Route::apiResource('fetch-progress', FetchingProgressController::class)->only(['index']);

    //Employee
    Route::apiResource('employees', EmployeeController::class)->only(['index', 'show']);

    // Step 1 — bulk upload. Both actions existed on (or were missing from) the
    // controllers with no route pointing at them, so neither was reachable.
    // Declared before the apiResources so the literal segment always wins.
    Route::post('employee-deductions/import', [EmployeeDeductionController::class, 'import'])
        ->middleware('payroll.step:' . PayrollStep::IMPORT);
    Route::post('employee-receivables/import', [EmployeeReceivableController::class, 'import'])
        ->middleware('payroll.step:' . PayrollStep::IMPORT);

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

    // Step 5 — the final list of employees for the run.
    Route::get('payroll-selections', [PayrollSelectionController::class, 'index'])
        ->middleware('payroll.step:' . PayrollStep::SELECTION);
    Route::put('payroll-selections', [PayrollSelectionController::class, 'update'])
        ->middleware('payroll.step:' . PayrollStep::SELECTION);
    Route::post('payroll-selections/reset', [PayrollSelectionController::class, 'reset'])
        ->middleware('payroll.step:' . PayrollStep::SELECTION);

    // Step 6 — generate, server side. Step 7 — review what was generated.
    Route::post('payroll-generate', [PayrollGenerationController::class, 'store'])
        ->middleware('payroll.step:' . PayrollStep::RECOMPUTE);
    Route::get('payroll-preview', [PayrollGenerationController::class, 'index']);

    //Night Differential
    Route::apiResource('night-differential-rules', NightDifferentialRuleController::class)->only(['index', 'store', 'show']);

    //Report
    Route::apiResource('payroll-reports', PayrollReportController::class)->only(['index', 'store']);
    Route::apiResource('payroll-summary', PayrollSummaryController::class)->only(['index', 'store']);
});