<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmDeductionImportRequest;
use App\Http\Requests\ImportDeductionsRequest;
use App\Http\Resources\EmployeeDeductionResource;
use App\Http\Resources\EmployeePayrollResource;
use App\Http\Resources\EmployeeReceivableResource;
use App\Http\Resources\PayrollRunResource;
use App\Http\Resources\PayrollSummaryResource;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Services\DeductionImportService;
use App\Services\EmployeeDeductionService;
use App\Services\EmployeeReceivableService;
use App\Services\InitialSalaryService;
use App\Services\PayrollGenerationService;
use App\Services\PayrollRunService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PayrollWorkflowController extends Controller
{
    public function __construct(
        private DeductionImportService $import,
        private EmployeeDeductionService $deductions,
        private EmployeeReceivableService $receivables,
        private InitialSalaryService $initialSalary,
        private PayrollGenerationService $generation,
        private PayrollRunService $runs,
    ) {}

    /**
     * Step 1 — left table: system-recorded deductions for the period
     * (carried forward from the previous period on first access).
     */
    public function recordedDeductions(PayrollPeriod $payroll_period): AnonymousResourceCollection
    {
        $this->deductions->carryForward($payroll_period->id);

        return EmployeeDeductionResource::collection(
            $this->deductions->listByPeriod($payroll_period->id)
        );
    }

    /**
     * Step 1 — right table: parse an uploaded CSV and return matched rows.
     * Nothing is persisted until confirm().
     */
    public function importDeductions(ImportDeductionsRequest $request, PayrollPeriod $payroll_period): JsonResponse
    {
        $contents = (string) $request->file('file')->get();

        return response()->json($this->import->parse($contents));
    }

    /**
     * Step 1 — persist the reconciled rows into period-scoped deductions.
     */
    public function confirmDeductions(ConfirmDeductionImportRequest $request, PayrollPeriod $payroll_period): JsonResponse
    {
        $written = $this->import->confirm($payroll_period->id, $request->validated()['rows']);

        return response()->json(['written' => $written]);
    }

    /**
     * Step 2 — manage deductions split by employee inclusion.
     * Query: ?inclusion=included|excluded|all (default all).
     */
    public function deductions(Request $request, PayrollPeriod $payroll_period): AnonymousResourceCollection
    {
        return EmployeeDeductionResource::collection(
            $this->deductions->listByPeriod($payroll_period->id, $this->inclusion($request))
        );
    }

    /**
     * Step 3 — manage receivables split by employee inclusion.
     */
    public function receivables(Request $request, PayrollPeriod $payroll_period): AnonymousResourceCollection
    {
        return EmployeeReceivableResource::collection(
            $this->receivables->listByPeriod($payroll_period->id, $this->inclusion($request))
        );
    }

    /**
     * Mid-step — compute + persist initial salaries (and system PERA/hazard)
     * for all included employees. Returns each with its eligibility flag.
     */
    public function computeInitialSalary(PayrollPeriod $payroll_period): JsonResponse
    {
        return response()->json([
            'data' => $this->initialSalary->computeAndPersist($payroll_period->id)->all(),
        ]);
    }

    /**
     * Step 4 — included employees whose initial salary is below the threshold.
     */
    public function belowThreshold(PayrollPeriod $payroll_period): JsonResponse
    {
        return response()->json([
            'threshold' => InitialSalaryService::THRESHOLD,
            'data' => $this->initialSalary->belowThreshold($payroll_period->id)->all(),
        ]);
    }

    /**
     * Step 5 — preview the payroll to process: period identity + counts.
     */
    public function preview(PayrollPeriod $payroll_period): JsonResponse
    {
        $summary = $this->initialSalary->summary($payroll_period->id);

        return response()->json([
            'period' => [
                'id' => $payroll_period->id,
                'month' => $payroll_period->month,
                'year' => $payroll_period->year,
                'employment_type' => $payroll_period->employment_type,
                'period_type' => $payroll_period->period_type,
                'payroll_type' => $payroll_period->payroll_type,
                'status' => $payroll_period->status,
            ],
            'counts' => [
                'included' => $summary->count(),
                'eligible' => $summary->filter->is_eligible->count(),
                'below_threshold' => $summary->reject->is_eligible->count(),
            ],
        ]);
    }

    /**
     * Step 6 — final list of employees eligible for payroll generation
     * (initial salary at or above the threshold).
     */
    public function eligibleEmployees(PayrollPeriod $payroll_period): JsonResponse
    {
        return response()->json([
            'threshold' => InitialSalaryService::THRESHOLD,
            'data' => $this->initialSalary->eligible($payroll_period->id)->all(),
        ]);
    }

    /**
     * Step 7 — generate the payroll run for eligible employees.
     */
    public function generate(Request $request, PayrollPeriod $payroll_period): PayrollRunResource
    {
        $run = $this->generation->generate($payroll_period->id, [
            'id' => $request->integer('actor_id') ?: null,
            'name' => $request->string('actor_name')->toString() ?: null,
        ]);

        return new PayrollRunResource($run->load('period'));
    }

    /**
     * Step 8 — preview/print: generated payslips + summary for a run.
     */
    public function generatedPayroll(PayrollRun $payroll_run): JsonResponse
    {
        $summary = $this->runs->summaryForRun($payroll_run->id);

        return response()->json([
            'run' => new PayrollRunResource($payroll_run),
            'summary' => $summary ? new PayrollSummaryResource($summary) : null,
            'payrolls' => EmployeePayrollResource::collection($this->runs->payrollsForRun($payroll_run->id)),
        ]);
    }

    /**
     * Step 8 — lock the run; freezes every payslip so it can no longer be adjusted.
     */
    public function lockPayroll(PayrollRun $payroll_run): PayrollRunResource
    {
        return new PayrollRunResource($this->runs->lockRun($payroll_run->id));
    }

    /**
     * Map the inclusion query param to a nullable boolean (null = all).
     */
    private function inclusion(Request $request): ?bool
    {
        return match ($request->string('inclusion', 'all')->toString()) {
            'included' => true,
            'excluded' => false,
            default => null,
        };
    }
}
