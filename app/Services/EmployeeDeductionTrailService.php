<?php

namespace App\Services;

use App\Contract\EmployeeDeductionTrailInterface;
use App\Data\EmployeeDeductionTrailData;
use App\Models\EmployeeDeductionTrail;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeDeductionTrailService
{
    public function __construct(private EmployeeDeductionTrailInterface $interface)
    {
        //Nothing
    }

    public function getAllPerPeriod(int $payroll_period_id, int $page, int $perPage): LengthAwarePaginator
    {
        return $this->interface->getAllPerPeriod($payroll_period_id, $page, $perPage);
    }

    public function getAllPagination(int $page, int $perPage): LengthAwarePaginator
    {
        return $this->interface->getAllPagination($page, $perPage);
    }

    public function create(EmployeeDeductionTrailData $data): EmployeeDeductionTrail
    {
        return $this->interface->create([
            'employee_deduction_id' => $data->employee_deduction_id,
            'total_term' => $data->total_term,
            'total_term_paid' => $data->total_term_paid,
            'amount_paid' => $data->amount_paid,
            'date_paid' => $data->date_paid,
            'balance' => $data->balance,
            'status' => $data->status,
            'remarks' => $data->remarks,
            'is_last_payment' => $data->is_last_payment,
            'is_adjustment' => $data->is_adjustment,
        ]);
    }

    public function find(int $id): ?EmployeeDeductionTrail
    {
        return $this->interface->find($id);
    }

}