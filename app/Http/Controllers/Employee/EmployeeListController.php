<?php

namespace App\Http\Controllers\Employee;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeListResource;
use App\Models\EmployeeList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Helpers\Logging;
use App\Models\EmployeeReceivable;
use App\Models\payroll_header;
use App\Models\PayrollHeaders;
use App\Models\ExcludedEmployee;
use App\Http\Controllers\GeneralPayroll\ComputationController;
use App\Helpers\Helpers;
use App\Models\GeneralPayroll;
use App\Http\Resources\EmployeeInformationResource;
use App\Helpers\Token;

class EmployeeListController extends Controller
{

    public function index(Request $request){
        if(isset($request->all)){
            $Emp = $this->allEmployees();
        }
        if(isset($request->with_active_pay)){
            $Emp = $this->withActivePay();
        }
        if(isset($request->designation)){
            $Emp = $this->withDesignation();
        }
        return response()->json([
            'Message'=>"List has been retrieved",
            'responseData'=>EmployeeInformationResource::collection($Emp),
            'statusCode'=>200,
        ], Response::HTTP_OK);

    }

    public function allEmployees(){
        $Emp = EmployeeList::all();
        return $Emp;
    }
    public function withActivePay(){
        $Emp = EmployeeList::whereNotIn('id', function ($query) {
            $query->select('employee_list_id')
                  ->from('excluded_employees');
        })->get();
        return $Emp;
    }

    public function withDesignation(){
        $designation = request()->designation;
        $Emp = EmployeeList::with(['getSalaries'])->get()->filter(function($row) use($designation) {
            return $row->getSalaries->contains(function ($salary) use ($designation) {
                return stripos($salary->employment_type, $designation) !== false;
            });
        });

        return $Emp;
    }


    //--------------------------------------------------------------------------

    public function AuthorizationPin(Request $request){
        try {
            $pin = $request->pinCode;

            if($pin == Token::UserInfo()['authorization_pin']){
                return response()->json([
                    'Message'=>"Access Granted",
                    'statusCode'=>200,
                ], Response::HTTP_OK);
            }
            return response()->json([
                'Message'=>"Access Denied",
                'statusCode'=>401,
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
