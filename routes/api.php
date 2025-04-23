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

Route::middleware('umis_server')->group(function () {
    Route::namespace('App\Http\Controllers\Employee')->group(function () {
        Route::get('FetchData', 'ImportEmployeeController@FetchList');

        Route::post("authorize_pin", "EmployeeListController@AuthorizationPin");
    });

    Route::namespace('App\Http\Controllers\Authentication')->group(function () {
        Route::post('Signin', 'LoginController@Signin');
    });
});

// Revision
Route::namespace('App\Http\Controllers')->group(function(){
    Route::namespace('Authentication')->group(function(){
        Route::post('sign-in', 'LoginController@store');
    });

    // Secure End Points
    Route::middleware('auth.token')->group(function(){
        Route::namespace('Authentication')->group(function(){
            Route::get('revalidate-session', 'LoginController@validateSession');
            Route::delete('sign-in', 'LoginController@destroy');
        });

        Route::namespace('Employee')->group(function(){
            Route::get("employee_index", "EmployeeListController@index");
            Route::get("employee_benefits", "EmployeeListController@benefitsList");
            Route::get("employee_deductions", "EmployeeListController@deductionList");

            // UNDEFINE PURPOSE
            Route::post('excluded-employee', 'ExcludedEmployeeController@store');
            Route::post('included-employee', 'ExcludedEmployeeController@update');
            
            Route::get('/employee-salaries', 'EmployeeSalaryController@index');
            Route::post('/exclude-employee', 'EmployeeSalaryController@excludeEmployee');
        });

        /**
         * General Payroll
         *
         */
        Route::namespace('GeneralPayroll')->group(function () {
            Route::get("validatePayroll/{employmenttype}", "PayrollController@validatePayroll");
            Route::get("activeTimeRecord", "PayrollController@ActiveTimeRecord");
            Route::get("payrollHeaders", "PayrollController@index");
            Route::post("generatePayroll", "PayrollController@computePayroll");
            Route::get("GeneralPayrollList/{id}", "PayrollController@GeneralPayrollList");
            Route::get("GeneralPayrollTrailsList/{id}", "PayrollController@GeneralPayrollTrails");
    
            Route::get("payrollSummary/{PayrollHeaderID}", "PayrollController@PayrollSummary");
            Route::post("Lockpayroll", "PayrollController@LockPayroll");
            Route::post("regenerate/{PayrollHeaderID}", "PayrollController@Regenerate");
    
            Route::post("autoregenerate", "PayrollController@AutoGeneratePayroll");
            Route::post("post_deductions", "PayrollController@post_deductions");
            Route::post("setActiveperiod", "PayrollController@setActiveperiod");
            Route::get("PeriodLists", "PayrollController@PeriodLists");
            Route::post("ChangeMonth", "PayrollController@ChangeMonth");
    
            Route::get("fetchNightDifferential", "PayrollController@fetchNightDifferential");
    
            Route::post("generate_payroll_step_1", "GeneratePayrollController@PayrollStep1");
            Route::post("generate_payroll_step_2", "GeneratePayrollController@PayrollStep2");
            Route::post("generate_payroll_step_3", "GeneratePayrollController@PayrollStep3");
            Route::post("generate_payroll_step_4", "GeneratePayrollController@PayrollStep4");
            Route::post("generate_payroll_permanent", "GeneratePayrollController@GeneratePermanentEmployeePayroll");
            Route::post("generate_payroll_job_order", "GeneratePayrollController@GenerateJobOrderEmployeePayroll");
        });
        
        /**
         * Adjustments
         *
         */
        Route::namespace('Adjustment')->group(function () {
            Route::get('adjustments', 'AdjustmentController@index');
            Route::get('adjustment/{id}', 'AdjustmentController@show');

            Route::get('adjustment-employee-deductions', 'EmployeeDeductionAdjustmentController@index');
            Route::get('adjustment-employee-deduction', 'EmployeeDeductionAdjustmentController@create');
            Route::post('adjustment-employee-deduction', 'EmployeeDeductionAdjustmentController@store');
            Route::get('adjustment-employee-deduction/{id}', 'EmployeeDeductionAdjustmentController@show');
        });

        /**
         * Trails
         *
         */
        Route::namespace('Trail')->group(function () {
            Route::get('employee-deduction-trails', 'EmployeeDeductionTrailController@index');
            Route::get('employee-deduction-trail', 'EmployeeDeductionTrailController@create');
            Route::post('employee-deduction-trail', 'EmployeeDeductionTrailController@store');
            Route::get('employee-deduction-trail/{id}', 'EmployeeDeductionTrailController@show');
            Route::delete('employee-deduction-trail/{id}', 'EmployeeDeductionTrailController@destroy');
        });

        /**
         * Settings
         * Deduction Group & Deduction
         */
        Route::namespace('Deduction')->group(function () {
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
            
            Route::get('/deductionsList', 'EmployeeDeductionController@getDeductionsStatusList');
            Route::delete('clearEmployeeDeductions/{id}', 'DeductionController@clearEmployeeDeductions');
            //Bulk EmployeeDeduction
            Route::post('/importemployeedeductions', 'EmployeeDeductionController@bulkimport');
            
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

        //  Receivables
        Route::namespace('Receivable')->group(function () {
            Route::get('receivables', 'ReceivableController@index');
            Route::post('receivable', 'ReceivableController@store');
            Route::get('receivable/{id}', 'ReceivableController@show');
            Route::put('receivable/{id}', 'ReceivableController@update');
            Route::delete('receivable/{id}', 'ReceivableController@destroy');
            Route::put('receivable-stop/{id}', 'ReceivableController@stop');
            
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

        /**
         * Reports
         *
         */
        Route::namespace('Reports')->group(function () {
            Route::get('reports', 'ReportsController@request');
            Route::get('reports-total-deductions', 'ReportsController@requestDeductions');
        });
    
        /** 
         * Fetch Data From UMIS
         */
        Route::namespace('UMIS')->group(function () {
            Route::get('fetch_dtr', 'EmployeeProfileController@index');
        });
    });
});




