<?php

namespace App\Services;

use App\Contract\NightDifferentialComputationInterface;
use App\Models\Employee;
use App\Models\NightDifferentialRules;
use App\Models\PayrollPeriod;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NightDifferentialComputationService
{
    public function __construct(private NightDifferentialComputationInterface $repository)
    {
        //
    }

    public function compute(int $payrollPeriodId, ?array $employeeIds = null): array
    {
        $payrollPeriod = PayrollPeriod::findOrFail($payrollPeriodId);

        $query = Employee::with([
            'employeeTimeRecords' => fn($q) => $q->where('payroll_period_id', $payrollPeriodId),
            'employeeComputedSalary' => fn($q) => $q->where('payroll_period_id', $payrollPeriodId),
            'employeeSalary' => fn($q) => $q->where('payroll_period_id', $payrollPeriodId),
        ]);

        if (!empty($employeeIds)) {
            $query->whereIn('id', $employeeIds);
        }

        $employees = $query->get();

        $results = [];

        DB::transaction(function () use ($employees, $payrollPeriod, $payrollPeriodId, &$results) {
            $periodDate = Carbon::create($payrollPeriod->year, $payrollPeriod->month, $payrollPeriod->period_start ?? 1)
                ->toDateString();

            foreach ($employees as $employee) {
                $timeRecord = $employee->employeeTimeRecords;
                $computedSalary = $employee->employeeComputedSalary;

                if (!$timeRecord || !$computedSalary) {
                    continue;
                }

                $nightDuties = json_decode($timeRecord->night_duties ?? '[]', true);

                if (empty($nightDuties)) {
                    continue;
                }

                $employmentType = $employee->employeeSalary?->employment_type
                    ?? $payrollPeriod->employment_type;

                $rule = NightDifferentialRules::where('employment_type', $employmentType)
                    ->where('is_active', true)
                    ->where('effective_date', '<=', $periodDate)
                    ->orderByDesc('effective_date')
                    ->first();

                if (!$rule) {
                    continue;
                }

                $hourlyRate = (float) $computedSalary->hourly_rate;
                $ratePercent = (float) $rule->rate_percent;
                $totalNightMinutes = 0.0;

                $this->repository->deleteNightDuties($employee->id, $payrollPeriodId);

                $nightDutyRows = [];

                foreach ($nightDuties as $duty) {
                    $timeIn = isset($duty['time_in']) ? Carbon::parse($duty['time_in']) : null;
                    $timeOut = isset($duty['time_out']) ? Carbon::parse($duty['time_out']) : null;

                    if (!$timeIn || !$timeOut) {
                        continue;
                    }

                    $nightMinutes = $this->intersectNightWindow(
                        $timeIn,
                        $timeOut,
                        $rule->start_time,
                        $rule->end_time
                    );

                    if ($nightMinutes <= 0) {
                        continue;
                    }

                    $totalNightMinutes += $nightMinutes;

                    $nightDutyRows[] = [
                        'employee_id' => $employee->id,
                        'payroll_period_id' => $payrollPeriodId,
                        'duty_date' => $duty['date'] ?? $timeIn->toDateString(),
                        'time_in' => $timeIn->toDateTimeString(),
                        'time_out' => $timeOut->toDateTimeString(),
                        'night_minutes' => round($nightMinutes, 2),
                        'night_hours' => round($nightMinutes / 60, 4),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $this->repository->insertNightDuties($nightDutyRows);

                $totalNightHours = round($totalNightMinutes / 60, 4);
                $totalNightAmount = round($hourlyRate * $totalNightHours * ($ratePercent / 100), 2);

                $this->repository->upsertComputation($employee->id, $payrollPeriodId, [
                    'total_night_hours' => $totalNightHours,
                    'total_night_amount' => $totalNightAmount,
                    'hourly_rate' => $hourlyRate,
                    'rate_percent' => $ratePercent,
                    'is_finalized' => false,
                    'computed_at' => now(),
                ]);

                $results[$employee->id] = [
                    'employee_id' => $employee->id,
                    'total_night_hours' => $totalNightHours,
                    'total_night_amount' => $totalNightAmount,
                    'hourly_rate' => $hourlyRate,
                    'rate_percent' => $ratePercent,
                ];
            }
        });

        return $results;
    }

    public function getComputations(int $payrollPeriodId, ?int $employeeId = null): Collection
    {
        return $this->repository->getByPeriod($payrollPeriodId, $employeeId);
    }

    public function sumByPeriod(int $payrollPeriodId): float
    {
        return $this->repository->sumByPeriod($payrollPeriodId);
    }

    /**
     * Returns minutes within the night window [startTime, endTime].
     * Handles midnight crossover (e.g., 22:00–06:00).
     */
    private function intersectNightWindow(Carbon $timeIn, Carbon $timeOut, string $startTime, string $endTime): float
    {
        $date = $timeIn->toDateString();

        $windowStart = Carbon::parse("{$date} {$startTime}");
        $windowEnd = Carbon::parse("{$date} {$endTime}");

        if ($windowEnd->lte($windowStart)) {
            $windowEnd->addDay();
        }

        $start = $timeIn->max($windowStart);
        $end = $timeOut->min($windowEnd);

        if ($end->lte($start)) {
            $windowStartNext = $windowStart->copy()->addDay();
            $windowEndNext = $windowEnd->copy()->addDay();

            $start = $timeIn->max($windowStartNext);
            $end = $timeOut->min($windowEndNext);

            if ($end->lte($start)) {
                return 0.0;
            }
        }

        return (float) $start->diffInMinutes($end);
    }
}
