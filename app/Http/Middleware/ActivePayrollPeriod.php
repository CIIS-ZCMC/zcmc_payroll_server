<?php

namespace App\Http\Middleware;

use App\Http\Resources\PayrollPeriodResource;
use Closure;
use Illuminate\Http\Request;
use App\Models\PayrollPeriod;
use Symfony\Component\HttpFoundation\Response;

class ActivePayrollPeriod
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $payrollPeriod = PayrollPeriod::where('is_active', true)->first();

        if (!$payrollPeriod) {
            $request->merge(['payroll_period' => null]);
            return $next($request);
        }

        $request->merge(['payroll_period' => new PayrollPeriodResource($payrollPeriod)]);
        return $next($request);
    }
}
