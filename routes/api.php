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


Route::namespace('App\Http\Controllers\Employee')->group(function () {
    Route::get('/employee-salaries', 'EmployeeSalaryController@index');
    Route::post('/exclude-employee', 'EmployeeSalaryController@excludeEmployee');
});

Route::namespace('App\Http\Controllers\Deduction')->group(function () {
    Route::get('/employee-deductions', 'EmployeeDeductionController@index');
    Route::post('/exclude-employee', 'EmployeeDeductionController@excludeEmployee');
    Route::get('/get-deductions', 'EmployeeDeductionController@getDeductions');
    Route::post('/get-employee-deductions', 'EmployeeDeductionController@getEmployeeDeductions');
    Route::get('/get-inactive-employee-deductions', 'EmployeeDeductionController@getInactiveEmployeeDeductions');
    Route::post('/update-employee-deductions', 'EmployeeDeductionController@updateDeduction');
    Route::post('/update-deduction-status', 'EmployeeDeductionController@updateStatus');
    Route::post('/add-employee-deductions', 'EmployeeDeductionController@storeDeduction');
});

Route::namespace('App\Http\Controllers\Receivable')->group(function () {
    Route::get('/employee-receivables', 'EmployeeReceivableController@index');
    Route::post('/exclude-employee', 'EmployeeReceivableController@excludeEmployee');
    Route::get('/get-receivables', 'EmployeeReceivableController@getReceivables');
    Route::post('/get-employee-receivables', 'EmployeeReceivableController@getEmployeeReceivables');
    Route::get('/get-inactive-employee-receivables', 'EmployeeReceivableController@getInactiveEmployeeReceivables');
    Route::post('/update-employee-receivables', 'EmployeeReceivableController@updateReceivable');
    Route::post('/update-receivable-status', 'EmployeeReceivableController@updateStatus');
    Route::post('/add-employee-receivables', 'EmployeeReceivableController@storeReceivable');
});
Route::group(['middleware' => ['stripTags']], function () {
    // Define your routes here
});
