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
use App\Http\Resources\DefaultDeductionEmployeeList;
use App\Http\Resources\ListOfEmployeeByDeductionResource;
use App\Models\Deduction;
use App\Models\EmployeeDeduction;
use App\Http\Controllers\Employee\ExcludedEmployeeController;
use App\Models\Receivable;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TimeRecordResource;


class EmployeeListController extends Controller
{

    private $excluded;
    public function __construct()
    {
        $this->excluded = new ExcludedEmployeeController();
    }
    public function index(Request $request)
    {
        $Emp = $this->allEmployees();


        if (isset($request->with_active_pay)) {
            $Emp = $this->withActivePay();
        }

        if (isset($request->designation)) {
            $Emp = $this->withDesignation();
        }

        if (isset($request->generalPayroll) && $request->generalPayroll) {
            $Emp = $this->QualifiedGeneralPayrollList();
        }

        if (isset($request->specialPayroll) && $request->specialPayroll) {
            $Emp = $this->QualifiedSpecialPayrollList();
        }
        if (isset($request->getEmployeeByDeduction) && $request->deductionId) {
            return $this->getEmployeebyDeduction($request->deductionId);
        }

        if (isset($request->isExcluded)) {
            $Emp = $this->isExcluded()['Emplist'];
        }

        if (isset($request->isIncluded)) {
            $Emp = $this->isIncluded();
        }

        if (isset($request->withDeduction)) {

        }
        if (isset($request->regenerateList)) {
            $Emp = $request->listofemployee;
        }


        return response()->json([
            'Message' => "List has been retrieved",
            'responseData' => EmployeeInformationResource::collection($Emp),
            'statusCode' => 200,
        ], Response::HTTP_OK);

    }

    public function allEmployees()
    {
        $Emp = EmployeeList::all();
        return $Emp;
    }

    public function getEmployeebyDeduction($deductionId)
    {
        $deduct = Deduction::find($deductionId);
        if (!$deduct) {
            return response()->json([
                'Message' => "Deduction not found",
                'responseData' => null,
                'statusCode' => 404,
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'Message' => "List has been retrieved",
            'responseData' => new DefaultDeductionEmployeeList($deduct),
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }
    public function QualifiedGeneralPayrollList()
    {
        $jobOrder = request()->jobOrder;
        $condition = "=";
        $month = request()->processMonth['month'];
        $year = request()->processMonth['year'];
        $JOfromPeriod = request()->processMonth['JOfromPeriod'];
        $JOtoPeriod = request()->processMonth['JOtoPeriod'];
        if ($jobOrder) {
            $condition = "=";
            $ValidateGeneralPayroll = DB::table('general_payrolls')
                ->select('employee_list_id')
                ->whereIn('payroll_headers_id', function ($query) use ($month, $year, $JOfromPeriod, $JOtoPeriod) {
                    $query->select('id')
                        ->from('payroll_headers')
                        ->where('month', $month)
                        ->where('year', $year)
                        ->where('fromPeriod', $JOfromPeriod)
                        ->where('toPeriod', $JOtoPeriod);
                });

        } else {
            $condition = "!=";

            $ValidateGeneralPayroll = DB::table('general_payrolls')
                ->select('employee_list_id')
                ->whereIn('payroll_headers_id', function ($query) use ($month, $year) {
                    $query->select('id')
                        ->from('payroll_headers')
                        ->where('month', $month)
                        ->where('year', $year);
                });

        }
        $ValidateGeneralPayroll = array_map(function ($row) {
            return $row->employee_list_id; // Use object access syntax
        }, $ValidateGeneralPayroll->get()->toArray());


        $Emp = EmployeeList::whereIn("id", function ($query) use ($condition) {
            $query->select("employee_list_id")
                ->from("time_records")
                ->where("is_active", 1);
        })->whereIn("id", function ($query) use ($condition) {
            $query->select("employee_list_id")
                ->from("employee_salaries")
                ->where("employment_type", $condition, "Job Order");
        })->whereNotIn("id", $ValidateGeneralPayroll)
            ->whereNotIn("id", $this->isExcluded()['ids'])
            ->get();

        return $Emp;
    }

    public function QualifiedSpecialPayrollList()
    {
        $month = request()->processMonth['month'];
        $year = request()->processMonth['year'];

        $jobOrder = request()->jobOrder;
        $condition = "=";
        if ($jobOrder) {
            $condition = "=";
        } else {
            $condition = "!=";
        }
        $Emp = EmployeeList::whereIn('id', function ($query) use ($month, $year) {
            $query->select('employee_list_id')
                ->from('excluded_employees')
                ->where('is_removed', 1);
        })->whereIn('id', function ($query) use ($month, $year) {
            $query->select('employee_list_id')
                ->from('general_payrolls')
                ->whereIn('payroll_headers_id', function ($subQuery) use ($month, $year) {
                    $subQuery->select('id')
                        ->from('payroll_headers')
                        ->where('month', $month)
                        ->where('year', $year);
                });
        })->whereIn("id", function ($query) use ($condition) {
            $query->select("employee_list_id")
                ->from("employee_salaries")
                ->where("employment_type", $condition, "Job Order");
        })
            ->get();

        $Emp2 = EmployeeList::whereIn("id", function ($query) use ($condition) {
            $query->select("employee_list_id")
                ->from("employee_salaries")
                ->where("employment_type", $condition, "Job Order");
        })->whereNotIn("id", function ($query) {
            $query->select("employee_list_id")
                ->from("general_payrolls")
                ->whereIn("payroll_headers_id", function ($subQuery) {
                    $subQuery->select("id")
                        ->from("payroll_headers")
                        ->where("month", request()->processMonth['month'])
                        ->where("year", request()->processMonth['year']);
                });
        })
            ->get();

        return $Emp->merge($Emp2);
    }

    public function withActivePay()
    {
        $Emp = EmployeeList::whereNotIn('id', function ($query) {
            $query->select('employee_list_id')
                ->from('excluded_employees');
        })->get();
        return $Emp;
    }

    public function withDesignation()
    {
        $designation = request()->designation;
        $Emp = EmployeeList::with(['getSalaries'])->get()->filter(function ($row) use ($designation) {
            return $row->getSalaries->contains(function ($salary) use ($designation) {
                return stripos($salary->employment_type, $designation) !== false;
            });
        });

        return $Emp;
    }

    public function isExcluded()
    {
        $response = $this->excluded->index();
        $decodedResponse = $response->getData(true);
        $excluded = $decodedResponse['responseData'];

        $ids = array_map(function ($row) {
            return $row['employee_list_id'];
        }, $excluded);

        return [
            'ids' => $ids,
            'Emplist' => EmployeeList::whereIn('id', $ids)->get()
        ];
    }

    public function isIncluded()
    {
        $Emp = EmployeeList::where('is_excluded', 0)->get();
        return $Emp;
    }




    //--------------------------------------------------------------------------

    public function AuthorizationPin(Request $request)
    {
        try {
            $pin = $request->pinCode;

            if ($pin == Token::UserInfo()['authorization_pin']) {
                return response()->json([
                    'Message' => "Access Granted",
                    'statusCode' => 200,
                ], Response::HTTP_OK);
            }
            return response()->json([
                'Message' => "Access Denied",
                'statusCode' => 401,
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function benefitsList()
    {
        return response()->json([
            'Message' => "List has been retrieved",
            'responseData' => Receivable::all(),
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }


    public function deductionList()
    {
        return response()->json([
            'Message' => "List has been retrieved",
            'responseData' => Deduction::all(),
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }


}
