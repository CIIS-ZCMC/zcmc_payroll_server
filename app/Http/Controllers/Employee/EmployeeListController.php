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
use App\Http\Controllers\Employee\ExcludedEmployeeController;
use App\Models\Receivable;

class EmployeeListController extends Controller
{

    private $excluded;
    public function __construct() {
        $this->excluded = new ExcludedEmployeeController();
    }
    public function index(Request $request){
         $Emp = $this->allEmployees();

        if(isset($request->with_active_pay)){
            $Emp = $this->withActivePay();
        }
        if(isset($request->designation)){
            $Emp = $this->withDesignation();
        }

        if(isset($request->generalPayroll) && $request->generalPayroll){
            $Emp = $this->QualifiedGeneralPayrollList();
        }

        if(isset($request->specialPayroll) && $request->specialPayroll){
            $Emp = $this->QualifiedSpecialPayrollList();
        }

        if(isset($request->isExcluded)){
            $Emp = $this->isExcluded()['Emplist'];
        }

        if(isset($request->withDeduction)){

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

    public function QualifiedGeneralPayrollList(){
        $jobOrder = request()->jobOrder;
        $condition = "=";
        if($jobOrder == "True"){
            $condition = "=";
        }else {
            $condition = "!=";
        }

        $Emp = EmployeeList::whereIn("id",function($query) use($condition){
            $query->select("employee_list_id")
                    ->from("time_records")
                    ->where("is_active",1);
        })->whereIn("id",function($query) use($condition){
            $query->select("employee_list_id")
                    ->from("employee_salaries")
                    ->where("employment_type",$condition,"Job Order");
        })->whereNotIn("employee_profile_id", $this->isExcluded()['ids'])
        ->get();
        return $Emp;
    }

    public function QualifiedSpecialPayrollList(){
        $month = request()->processMonth['month'];
        $year = request()->processMonth['year'];

        $jobOrder = request()->jobOrder;
        $condition = "=";
        if($jobOrder == "True"){
            $condition = "=";
        }else {
            $condition = "!=";
        }
        $Emp = EmployeeList::whereIn('id', function ($query) use($month,$year) {
            $query->select('employee_list_id')
                  ->from('excluded_employees')
                  ->where('is_removed', 1);
        })->whereIn('id', function ($query) use($month,$year) {
            $query->select('employee_list_id')
                  ->from('general_payrolls')
                  ->whereIn('payroll_headers_id', function ($subQuery) use($month,$year) {
                      $subQuery->select('id')
                               ->from('payroll_headers')
                               ->where('month', $month)
                               ->where('year', $year);
                  });
        })->whereIn("id",function($query) use($condition){
            $query->select("employee_list_id")
                    ->from("employee_salaries")
                    ->where("employment_type",$condition,"Job Order");
        })->get();

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

    public function isExcluded(){
        $response = $this->excluded->index();
        $decodedResponse = $response->getData(true);
        $excluded =  $decodedResponse['responseData'];

        $ids = array_map(function($row){
            return $row['employee_list_id'];
        },$excluded);
        return [
            'ids'=>$ids,
            'Emplist'=>EmployeeList::whereIn('employee_profile_id',$ids)->get()
        ];



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

    public function benefitsList(){
        return response()->json([
            'Message'=>"List has been retrieved",
            'responseData'=>Receivable::all(),
            'statusCode'=>200,
        ], Response::HTTP_OK);
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
