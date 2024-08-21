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
            Route::post('/get-employee-deductions', 'EmployeeDeductionController@getEmployeeDeductions');
            Route::post('/get-inactive-employee-deductions', 'EmployeeDeductionController@getInactiveEmployeeDeductions');
            Route::post('/get-suspended-employee-deductions', 'EmployeeDeductionController@getSuspendedEmployeeDeductions');
            Route::post('/update-employee-deductions', 'EmployeeDeductionController@updateDeduction');
            Route::post('/update-deduction-status', 'EmployeeDeductionController@updateStatus');
            Route::post('/add-employee-deductions', 'EmployeeDeductionController@storeDeduction');
        });

Route::
        namespace('App\Http\Controllers\Receivable')->group(function () {
            Route::get('/employee-receivables', 'EmployeeReceivableController@index');
            Route::post('/exclude-employee', 'EmployeeReceivableController@excludeEmployee');
            Route::get('/get-receivables', 'EmployeeReceivableController@getReceivables');
            Route::post('/get-employee-receivables', 'EmployeeReceivableController@getEmployeeReceivables');
            Route::post('/get-inactive-employee-receivables', 'EmployeeReceivableController@getInactiveEmployeeReceivables');
            Route::post('/get-suspended-employee-receivables', 'EmployeeReceivableController@getSuspendedEmployeeReceivables');
            Route::post('/update-employee-receivables', 'EmployeeReceivableController@updateReceivable');
            Route::post('/update-receivable-status', 'EmployeeReceivableController@updateStatus');
            Route::post('/add-employee-receivables', 'EmployeeReceivableController@storeReceivable');
        });

Route::group(['middleware' => ['stripTags']], function () {
    Route::
            namespace('App\Http\Controllers\Deduction')->group(function () {
                Route::get('deduction-groups', 'DeductionGroupController@index');
                Route::post('deduction-group', 'DeductionGroupController@store');
                Route::get('deduction-group/{id}', 'DeductionGroupController@show');
                Route::put('deduction-group/{id}', 'DeductionGroupController@update');
                Route::delete('deduction-group/{id}', 'DeductionGroupController@destroy');


                Route::get('deductions', 'DeductionController@index');
                Route::post('deduction', 'DeductionController@store');
                Route::get('deduction/{id}', 'DeductionController@show');
                Route::put('deduction/{id}', 'DeductionController@updapte');
                Route::delete('deduction/{id}', 'DeductionController@destroy');
            });

    Route::
            namespace('App\Http\Controllers\Receivable')->group(function () {
                Route::get('receivables', 'ReceivableController@index');
                Route::post('receivable', 'ReceivableController@store');
                Route::get('receivable/{id}', 'ReceivableController@show');
                Route::put('receivable/{id}', 'ReceivableController@update');
                Route::delete('receivable/{id}', 'ReceivableController@destroy');
            });
});
