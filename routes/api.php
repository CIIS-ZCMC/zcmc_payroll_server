<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Employees\EmployeeController;
use App\Http\Controllers\Employees\EmployeeDeductionController;
use App\Http\Controllers\Payroll\EmployeePayrollController;
use App\Http\Controllers\Payroll\GeneralPayrollController;
use App\Http\Controllers\Payroll\PayrollPeriodController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Settings\DeductionController;
use App\Http\Controllers\Settings\DeductionGroupController;
use App\Http\Controllers\Settings\DeductionRuleController;
use App\Http\Controllers\Settings\ReceivableController;
use App\Http\Controllers\UMIS\EmployeeProfileController;
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

Route::middleware('umis_server')->group(function () {
    Route::namespace('App\Http\Controllers\Employee')->group(function () {
        Route::get('FetchData', 'ImportEmployeeController@FetchList');

        Route::post("authorize_pin", "EmployeeListController@AuthorizationPin");
    });

    Route::namespace('App\Http\Controllers\Authentication')->group(function () {
        Route::post('Signin', 'LoginController@Signin');
    });
});

Route::
        namespace('App\Http\Controllers')->group(function () {
            // Route::namespace('Authentication')->group(function () {
            //     Route::post('sign-in', 'LoginController@store');
            // });
        
            // Secure End Points
            // Route::middleware('auth.token')->group(function () {
        

            //     Route::namespace('Authentication')->group(function () {
            //         Route::get('revalidate-session', 'LoginController@validateSession');
            //         Route::delete('sign-out', 'LoginController@destroy');
        
            //         Route::get("ReAuthenticate", "LoginController@ReAuthenticate");
            //     });
        
            //     Route::namespace('Employee')->group(function () {
            //         Route::get("employee_index", "EmployeeListController@index");
            //         Route::get("employee_benefits", "EmployeeListController@benefitsList");
            //         Route::get("employee_deductions", "EmployeeListController@deductionList");
        
            //         Route::get('/employee-salaries', 'EmployeeSalaryController@index');
            //         Route::post('/exclude-employee', 'EmployeeSalaryController@excludeEmployee');
            //     });
        
            //     /**
            //      * Deductions
            //      *
            //      */
            //     Route::namespace('Deduction')->group(function () {
            //         Route::get('/employee-deductions', 'EmployeeDeductionController@index');
            //         Route::post('/exclude-employee', 'EmployeeDeductionController@excludeEmployee');
            //         Route::get('/get-deductions', 'EmployeeDeductionController@getDeductions');
            //         Route::get('/get-employee-deductions/{id}', 'EmployeeDeductionController@getEmployeeDeductions');
            //         Route::get('/get-inactive-employee-deductions/{id}', 'EmployeeDeductionController@getInactiveEmployeeDeductions');
            //         Route::get('/get-suspended-employee-deductions/{id}', 'EmployeeDeductionController@getSuspendedEmployeeDeductions');
            //         Route::post('/update-employee-deductions', 'EmployeeDeductionController@updateDeduction');
            //         Route::post('/update-deduction-status', 'EmployeeDeductionController@updateStatus');
            //         Route::post('/add-employee-deductions', 'EmployeeDeductionController@storeDeduction');
            //     });
        
            //     /**
            //      * Adjustment
            //      */
        
            //     Route::namespace('Adjustment')->group(function () {
        
            //         Route::get('adjustment-employee-deductions', 'EmployeeDeductionAdjustmentController@index');
            //         Route::get('adjustment-employee-deduction', 'EmployeeDeductionAdjustmentController@create');
            //         Route::post('adjustment-employee-deduction', 'EmployeeDeductionAdjustmentController@store');
            //         Route::get('adjustment-employee-deduction/{id}', 'EmployeeDeductionAdjustmentController@show');
            //     });
        
            //     /**
            //      * Receivables
            //      *
            //      */
            //     Route::namespace('Receivable')->group(function () {
            //         Route::get('/employee-receivables', 'EmployeeReceivableController@index');
            //         Route::post('/exclude-employee', 'EmployeeReceivableController@excludeEmployee');
            //         Route::get('/get-receivables', 'EmployeeReceivableController@getReceivables');
            //         Route::get('/get-employee-receivables/{id}', 'EmployeeReceivableController@getEmployeeReceivables');
            //         Route::get('/get-inactive-employee-receivables/{id}', 'EmployeeReceivableController@getInactiveEmployeeReceivables');
            //         Route::get('/get-suspended-employee-receivables/{id}', 'EmployeeReceivableController@getSuspendedEmployeeReceivables');
            //         Route::post('/update-employee-receivables', 'EmployeeReceivableController@updateReceivable');
            //         Route::post('/update-receivable-status', 'EmployeeReceivableController@updateStatus');
            //         Route::post('/add-employee-receivables', 'EmployeeReceivableController@storeReceivable');
        
            //         Route::get('receivables', 'ReceivableController@index');
            //         Route::post('receivable', 'ReceivableController@store');
            //         Route::get('receivable/{id}', 'ReceivableController@show');
            //         Route::put('receivable/{id}', 'ReceivableController@update');
            //         Route::delete('receivable/{id}', 'ReceivableController@destroy');
            //         Route::put('receivable-stop/{id}', 'ReceivableController@stop');
            //     });
        
            //     /**
            //      * General Payroll
            //      *
            //      */
            //     Route::namespace('GeneralPayroll')->group(function () {
            //         Route::get("validatePayroll/{employmenttype}", "PayrollController@validatePayroll");
            //         Route::get("activeTimeRecord", "PayrollController@ActiveTimeRecord");
            //         Route::get("payrollHeaders", "PayrollController@index");
            //         Route::post("generatePayroll", "PayrollController@computePayroll");
            //         Route::get("GeneralPayrollList/{id}", "PayrollController@GeneralPayrollList");
            //         Route::get("GeneralPayrollTrailsList/{id}", "PayrollController@GeneralPayrollTrails");
        
            //         Route::get("payrollSummary/{PayrollHeaderID}", "PayrollController@PayrollSummary");
            //         Route::post("Lockpayroll", "PayrollController@LockPayroll");
            //         Route::post("regenerate/{PayrollHeaderID}", "PayrollController@Regenerate");
        
            //         Route::post("autoregenerate", "PayrollController@AutoGeneratePayroll");
            //         Route::post("post_deductions", "PayrollController@post_deductions");
            //         Route::post("setActiveperiod", "PayrollController@setActiveperiod");
            //         Route::get("PeriodLists", "PayrollController@PeriodLists");
            //         Route::post("ChangeMonth", "PayrollController@ChangeMonth");
        
            //         Route::get("fetchNightDifferential", "PayrollController@fetchNightDifferential");
            //     });
        
            //     /**
            //      * Trails
            //      *
            //      */
            //     Route::namespace('Trail')->group(function () {
            //         Route::get('employee-deduction-trails', 'EmployeeDeductionTrailController@index');
            //         Route::get('employee-deduction-trail', 'EmployeeDeductionTrailController@create');
            //         Route::post('employee-deduction-trail', 'EmployeeDeductionTrailController@store');
            //         Route::get('employee-deduction-trail/{id}', 'EmployeeDeductionTrailController@show');
            //         Route::delete('employee-deduction-trail/{id}', 'EmployeeDeductionTrailController@destroy');
            //     });
        
            //     /**
            //      * Reports
            //      *
            //      */
            //     Route::namespace('Reports')->group(function () {
            //         Route::get('reports', 'ReportsController@request');
            //         Route::get('reports-total-deductions', 'ReportsController@requestDeductions');
            //     });
        
            // });
        
        });



//Version 2 Api's
Route::post('sign-in', [LoginController::class, 'store']);

Route::middleware('auth.token')->group(function () {
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

    //Payroll Period
    Route::apiResource('payroll-periods', PayrollPeriodController::class)->only(['index']);

    //Employee Payroll
    Route::apiResource('employee-payrolls', EmployeePayrollController::class)->only(['index', 'store']);

    //General Payroll
    Route::apiResource('general-payrolls', GeneralPayrollController::class);
});

//Report
Route::apiResource('payroll-reports', ReportsController::class)->only(['index']);

