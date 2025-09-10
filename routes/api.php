<?php

use App\Http\Controllers\Authentication\AuthenticationController;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Employee\EmployeeAdjustmentController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeeDeductionController;
use App\Http\Controllers\Employee\EmployeeReceivableController;
use App\Http\Controllers\Employee\EmployeeTimeRecordController;
use App\Http\Controllers\Payroll\EmployeePayrollController;
use App\Http\Controllers\Payroll\GeneralPayrollController;
use App\Http\Controllers\Payroll\PayrollPeriodController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Settings\DeductionController;
use App\Http\Controllers\Settings\DeductionGroupController;
use App\Http\Controllers\Settings\DeductionRuleController;
use App\Http\Controllers\Settings\ReceivableController;
use App\Http\Controllers\Trail\EmployeeDeductionTrailController;
use App\Http\Controllers\UMIS\EmployeeProfileController;
use App\Models\EmployeeDeductionTrail;
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
    Route::put('deduction-stop/{id}', [DeductionController::class, 'stop']);
    Route::apiResource('deductions', DeductionController::class);
    Route::apiResource('deduction-rules', DeductionRuleController::class);
    Route::apiResource('deduction-groups', DeductionGroupController::class);
    Route::apiResource('receivables', ReceivableController::class);

    // UMIS
    Route::get('fetch_record_step_1', [EmployeeProfileController::class, 'fetchStep1']);
    Route::post('fetch_record_step_2', [EmployeeProfileController::class, 'fetchStep2']);
    Route::post('fetch_record_step_3', [EmployeeProfileController::class, 'fetchStep3']);
    Route::post('fetch_record_step_4', [EmployeeProfileController::class, 'fetchStep4']);

    //Employee
    Route::apiResource('employees', EmployeeController::class)->only(['index']);
    Route::apiResource('employee-deductions', EmployeeDeductionController::class);
    Route::apiResource('employee-receivables', EmployeeReceivableController::class);
    Route::apiResource('employee-time-records', EmployeeTimeRecordController::class);
    Route::apiResource('employee-adjustments', EmployeeAdjustmentController::class)->only(['index', 'store', 'show']);
    Route::apiResource('employee-deduction-trail', EmployeeDeductionTrailController::class)->only(['index', 'store', 'show', 'destroy']);

    //Payroll Period
    Route::apiResource('payroll-periods', PayrollPeriodController::class)->only(['index','destroy']);
    Route::get("payroll-period-lists", [PayrollPeriodController::class, 'payrollPeriodList']);

    //Employee Payroll
    Route::apiResource('employee-payrolls', EmployeePayrollController::class)->only(['index', 'store', 'show']);

    //General Payroll
    Route::apiResource('general-payrolls', GeneralPayrollController::class)->only(['index', 'update', 'destroy']);

    //Report
    Route::apiResource('payroll-reports', ReportsController::class)->only(['index']);
});