<?php

namespace App\Http\Middleware;

use App\Enums\PayrollStep;
use App\Models\PayrollPeriod;
use App\Models\PayrollProcess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Refuses a step's endpoints to a run that has not reached that step.
 *
 * Used as `payroll.step:1`, `payroll.step:5`, and so on. The run is identified
 * the way everything else in this system identifies it — by payroll_period_id
 * plus payroll_type. When the request does not carry payroll_type, it is taken
 * from the period's own column, which is what distinguishes a Regular run from
 * a Job Order one.
 *
 * Deliberately fails *open* when there is no process row: a period being
 * maintained outside the stepped workflow has no step to be behind. It fails
 * closed only when a process row exists and is genuinely earlier than the step
 * being requested.
 */
class PayrollStepGate
{
    public function handle(Request $request, Closure $next, $requiredStep)
    {
        $requiredStep = (int) $requiredStep;

        $payrollPeriodId = $request->input('payroll_period_id');

        if (! $payrollPeriodId) {
            return $next($request);
        }

        $payrollType = $request->input('payroll_type');

        if ($payrollType === null) {
            $payrollType = PayrollPeriod::where('id', $payrollPeriodId)->value('payroll_type');
        }

        if ($payrollType === null) {
            return $next($request);
        }

        $process = PayrollProcess::where('payroll_period_id', $payrollPeriodId)
            ->where('payroll_type', $payrollType)
            ->first();

        if (! $process) {
            return $next($request);
        }

        if ((int) $process->current_step < $requiredStep) {
            return response()->json([
                'message' => sprintf(
                    'This payroll run is on step %d (%s). Step %d (%s) is not available yet.',
                    (int) $process->current_step,
                    PayrollStep::label((int) $process->current_step),
                    $requiredStep,
                    PayrollStep::label($requiredStep)
                ),
                'success' => false,
            ], Response::HTTP_CONFLICT);
        }

        return $next($request);
    }
}
