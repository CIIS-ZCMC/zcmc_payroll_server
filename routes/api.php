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

Route::namespace('App\Http\Controllers\Employee')->group(function () {
    Route::get('/employee-salaries', 'EmployeeSalaryController@index');
    Route::post('/exclude-employee', 'EmployeeSalaryController@excludeEmployee');
});

Route::namespace('App\Http\Controllers\Deduction')->group(function () {
    Route::get('/employee-deductions', 'EmployeeDeductionController@index');
    Route::post('/exclude-employee', 'EmployeeDeductionController@excludeEmployee');
    Route::post('/get-deductions', 'EmployeeDeductionController@getDeductions');
    Route::post('/get-employee-deductions', 'EmployeeDeductionController@getEmployeeDeductions');
    Route::post('/update-employee-deductions', 'EmployeeDeductionController@updateDeduction');
    Route::post('/add-employee-deductions', 'EmployeeDeductionController@storeDeduction');
});

Route::namespace('App\Http\Controllers\Receivable')->group(function () {
    Route::get('/employee-receivables', 'EmployeeReceivablesController@index');
    Route::post('/exclude-employee', 'EmployeeReceivablesController@excludeEmployee');
    Route::post('/get-receivables', 'EmployeeReceivablesController@getReceivables');
    Route::post('/get-employee-receivables', 'EmployeeReceivablesController@getEmployeeReceivables');
    Route::post('/update-employee-receivables', 'EmployeeReceivablesController@updateReceivables');
    Route::post('/add-employee-receivables', 'EmployeeReceivablesController@storeReceivables');
});
Route::group(['middleware' => ['stripTags']], function () {
    // Define your routes here
});
