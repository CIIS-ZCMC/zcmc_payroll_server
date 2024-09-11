<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('umis_server')->group(function () {
    Route::namespace('App\Http\Controllers\Employee')->group(function () {
        Route::get('FetchData', 'ImportEmployeeController@FetchList');

        Route::post("authorize_pin", "EmployeeListController@AuthorizationPin");
    });

    Route::namespace('App\Http\Controllers\Authentication')->group(function () {
        Route::post('Signin', 'LoginController@Signin');
    });
});


Route::middleware('auth.token')->group(function () {

    Route::namespace('App\Http\Controllers\Employee')->group(function () {
        Route::get("employee_index", "EmployeeListController@index");
        Route::get("employee_benefits", "EmployeeListController@benefitsList");

    });
    /**
     * Deductions
     *
     */

    /**
     * General Payroll
     *
     */
    Route::namespace('App\Http\Controllers\GeneralPayroll')->group(function () {
        Route::get("activeTimeRecord", "PayrollController@ActiveTimeRecord");
        Route::get("payrollHeaders", "PayrollController@index");
        Route::post("generatePayroll", "PayrollController@computePayroll");
        Route::get("GeneralPayrollList/{id}", "PayrollController@GeneralPayrollList");
        Route::get("GeneralPayrollTrailsList/{id}", "PayrollController@GeneralPayrollTrails");
    });

    /**
     * Settings
     *
     */
    //  Deduction Group & Deduction
    Route::namespace('App\Http\Controllers\Deduction')->group(function () {
        Route::get('deduction-groups', 'DeductionGroupController@index');
        Route::post('deduction-group', 'DeductionGroupController@store');
        Route::get('deduction-group/{id}', 'DeductionGroupController@show');
        Route::put('deduction-group/{id}', 'DeductionGroupController@update');
        Route::delete('deduction-group/{id}', 'DeductionGroupController@destroy');

        Route::get('deductions', 'DeductionController@index');
        Route::post('deduction', 'DeductionController@store');
        Route::get('deduction/{id}', 'DeductionController@show');
        Route::put('deduction/{id}', 'DeductionController@update');
        Route::delete('deduction/{id}', 'DeductionController@destroy');
        Route::put('deduction-stop/{id}', 'DeductionController@stop');

        Route::get('deduction-employment-type', 'DeductionController@getEmploymentType');
        Route::get('deduction-designation', 'DeductionController@getDesignation');
        Route::get('deduction-area', 'DeductionController@getArea');
        Route::get('deduction-salary-grade', 'DeductionController@getSalaryGrade');
    });

    //  Receivables
    Route::namespace('App\Http\Controllers\Receivable')->group(function () {
        Route::get('receivables', 'ReceivableController@index');
        Route::post('receivable', 'ReceivableController@store');
        Route::get('receivable/{id}', 'ReceivableController@show');
        Route::put('receivable/{id}', 'ReceivableController@update');
        Route::delete('receivable/{id}', 'ReceivableController@destroy');
        Route::put('receivable-stop/{id}', 'ReceivableController@stop');
    });
});


Route::
        namespace('App\Http\Controllers\Employee')->group(function () {
            Route::get('/employee-salaries', 'EmployeeSalaryController@index');
            Route::post('/exclude-employee', 'EmployeeSalaryController@excludeEmployee');
        });

Route::
        namespace('App\Http\Controllers\Deduction')->group(function () {
            Route::get('/employee-deductions', 'EmployeeDeductionController@index');
            Route::post('/exclude-employee', 'EmployeeDeductionController@excludeEmployee');
            Route::get('/get-deductions', 'EmployeeDeductionController@getDeductions');
            Route::get('/get-employee-deductions/{id}', 'EmployeeDeductionController@getEmployeeDeductions');
            Route::get('/get-inactive-employee-deductions/{id}', 'EmployeeDeductionController@getInactiveEmployeeDeductions');
            Route::get('/get-suspended-employee-deductions/{id}', 'EmployeeDeductionController@getSuspendedEmployeeDeductions');
            Route::post('/update-employee-deductions', 'EmployeeDeductionController@updateDeduction');
            Route::post('/update-deduction-status', 'EmployeeDeductionController@updateStatus');
            Route::post('/add-employee-deductions', 'EmployeeDeductionController@storeDeduction');
        });

Route::
        namespace('App\Http\Controllers\Receivable')->group(function () {
            Route::get('/employee-receivables', 'EmployeeReceivableController@index');
            Route::post('/exclude-employee', 'EmployeeReceivableController@excludeEmployee');
            Route::get('/get-receivables', 'EmployeeReceivableController@getReceivables');
            Route::get('/get-employee-receivables/{id}', 'EmployeeReceivableController@getEmployeeReceivables');
            Route::get('/get-inactive-employee-receivables/{id}', 'EmployeeReceivableController@getInactiveEmployeeReceivables');
            Route::get('/get-suspended-employee-receivables/{id}', 'EmployeeReceivableController@getSuspendedEmployeeReceivables');
            Route::post('/update-employee-receivables', 'EmployeeReceivableController@updateReceivable');
            Route::post('/update-receivable-status', 'EmployeeReceivableController@updateStatus');
            Route::post('/add-employee-receivables', 'EmployeeReceivableController@storeReceivable');
        });
