<?php

namespace App\Services;

use App\Contract\DeductionInterface;
use App\Data\DeductionData;
use App\Models\Deduction;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DeductionService
{
    public function __construct(private DeductionInterface $interface)
    {
        //nothing
    }

    public function getAll(): Collection
    {
        return $this->interface->getAll();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function create(DeductionData $data): Deduction
    {
        return $this->interface->create([
            'deduction_uuid' => $data->deduction_uuid,
            'deduction_group_id' => $data->deduction_group_id,
            'name' => $data->name,
            'code' => $data->code,
            'type' => $data->type,
            'hasDate' => $data->hasDate,
            'date_start' => $data->date_start,
            'date_end' => $data->date_end,
            'condition_operator' => $data->condition_operator,
            'condition_value' => $data->condition_value,
            'percent_value' => $data->percent_value,
            'fixed_amount' => $data->fixed_amount,
            'billing_cycle' => $data->billing_cycle,
            'status' => $data->status,
        ]);
    }

    public function update(int $id, DeductionData $data): Deduction
    {
        return $this->interface->update($id, [
            'name' => $data->name,
            'code' => $data->code,
            'type' => $data->type,
            'hasDate' => $data->hasDate,
            'date_start' => $data->date_start,
            'date_end' => $data->date_end,
            'condition_operator' => $data->condition_operator,
            'condition_value' => $data->condition_value,
            'percent_value' => $data->percent_value,
            'fixed_amount' => $data->fixed_amount,
            'billing_cycle' => $data->billing_cycle,
            'status' => $data->status,
        ]);
    }

    public function find(int $id): ?Deduction
    {
        return $this->interface->find($id);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }

    public function findWithFilters(int $id, int $payroll_period_id, int $month, int $year, string $employment_type, string $period_type)
    {
        return Deduction::with([
            'employeeDeductions' => function ($query) use ($payroll_period_id, $month, $year, $employment_type, $period_type) {
                // Try current period first
                $currentCount = EmployeeDeduction::where('payroll_period_id', $payroll_period_id)->count();
                
                if ($currentCount > 0) {
                    $query->where('payroll_period_id', $payroll_period_id);
                    return;
                }
         
                // Find previous period
                $previousPeriod = $this->findPreviousPeriod($month, $year, $employment_type, $period_type);
                
                if ($previousPeriod) {
                    $query->where('payroll_period_id', $previousPeriod->id);
                }
            },
            'employeeDeductions.employee'
        ])->where('id', $id)->first();
    }

    private function findPreviousPeriod(int $month, int $year, string $employment_type, string $period_type)
    {
        if ($period_type === 'second_half') {   
            // Same month, first half
            return PayrollPeriod::where('month', $month)
                ->where('year', $year)
                ->where('employment_type', $employment_type)
                ->where('period_type', 'first_half')
                ->first();
        }
        
        // First half - get previous month's second half
        $previousMonth = $month - 1;
        $previousYear = $year;

        if ($month == 1) {
            $previousMonth = 12;
            $previousYear = $year - 1;
        }
        
        return PayrollPeriod::where('month', $previousMonth)
            ->where('year', $previousYear)
            ->where('employment_type', $employment_type)
            ->where('period_type', 'second_half')
            ->first();
    }
}
