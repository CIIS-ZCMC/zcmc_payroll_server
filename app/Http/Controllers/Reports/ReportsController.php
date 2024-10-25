<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralPayroll\PayrollController;
use App\Http\Resources\GeneralPayrollResources;
use App\Models\PayrollHeaders;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    private $CONTROLLER_NAME = 'Report';
    private $PLURAL_MODULE_NAME = 'reports';
    private $SINGULAR_MODULE_NAME = 'report';

    public function request(Request $request)
    {
        try {
            $find = PayrollHeaders::where('month', $request->month)
                ->where('year', $request->year)
                ->where('employment_type', $request->employment_type);

            if ($request->employment_type === 'job order') {
                // Check if salary period is 1-15 or 16-30/31
                if ($request->salary_period === '1-15') {
                    $find->where('fromPeriod', 1)
                        ->where('toPeriod', 15);
                } elseif ($request->salary_period === '16-30/31') {
                    $find->where('fromPeriod', 16)
                        ->where('toPeriod', 30); // You can also adjust for months with 31 days if needed
                }
            }

            $PayrollHeader = $find->first();

            $PayrollController = new PayrollController();
            return $PayrollController->GeneralPayrollList($PayrollHeader->id);

        } catch (\Throwable $th) {

            Helpers::errorLog($this->CONTROLLER_NAME, 'index', $th->getMessage());
            return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
