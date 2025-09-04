<?php

namespace App\Services;

use App\Contract\EmployeeDeductionInterface;
use App\Data\EmployeeDeductionData;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeDeductionService
{
    public function __construct(private EmployeeDeductionInterface $interface)
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

    public function index(Request $request): Collection|LengthAwarePaginator
    {
        $mode = $request->mode;
        if ($mode === 'paginate') {
            return $this->paginate($request->perPage, $request->page);
        }

        return $this->getAll();
    }

    public function create(EmployeeDeductionData $data): EmployeeDeduction
    {
        $amount = $data->amount;
        $percentage = $data->percentage;

        if ($percentage > 0) {
            $base_salary = EmployeeTimeRecord::where('payroll_period_id', $data->payroll_period_id)
                ->where('employee_id', $data->employee_id)
                ->first()
                ->base_salary ?? 0;

            $percentageValue = $percentage / 100;
            $amount = round($base_salary * $percentageValue, 2);
        }

        return $this->interface->create([
            'payroll_period_id' => $data->payroll_period_id,
            'employee_id' => $data->employee_id,
            'deduction_id' => $data->deduction_id,
            'frequency' => $data->frequency,
            'amount' => $amount,
            'percentage' => $percentage,
            'date_from' => $data->date_from,
            'date_to' => $data->date_to,
            'with_terms' => $data->with_terms,
            'total_term' => $data->total_term,
            'total_paid' => $data->total_paid,
            'is_default' => $data->is_default,
            'isDifferential' => $data->isDifferential,
            'reason' => $data->reason,
            'status' => $data->status,
            'willDeduct' => $data->willDeduct,
            'stopped_at' => $data->stopped_at,
            'completed_at' => $data->completed_at,
        ]);
    }

    public function upsert(array $data): int
    {
        $records = array_map(fn(EmployeeDeductionData $data) => $data->toArray(), $data);
        return $this->interface->upsert($records);
    }

    public function store(array $dtos)
    {
        if (count($dtos) === 1) {
            return $this->create($dtos[0]);
        }

        return $this->upsert($dtos);
    }

    public function update(int $id, array $data): EmployeeDeduction
    {
        $amount = $data['amount'];
        $percentage = $data['percentage'];

        if ($percentage > 0) {
            $base_salary = EmployeeTimeRecord::where('payroll_period_id', $data['payroll_period_id'])
                ->where('employee_id', $data['employee_id'])
                ->first()
                ->base_salary ?? 0;

            $percentageValue = $percentage / 100;
            $amount = round($base_salary * $percentageValue, 2);
        }

        return $this->interface->update($id, [
            'frequency' => $data['frequency'],
            'amount' => $amount,
            'percentage' => $percentage,
            'with_terms' => $data['with_terms'],
            'total_term' => $data['total_term'],
            'is_default' => $data['is_default'],
            'reason' => $data['reason'],
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }

    public function complete(int $id): EmployeeDeduction
    {
        return $this->interface->complete($id);
    }

    public function stop(int $id): EmployeeDeduction
    {
        return $this->interface->stop($id);
    }

    private function checkPayrollPeriodLock(): array
    {
        $payrollPeriod = PayrollPeriod::where('is_active', 1)->first();

        if ($payrollPeriod && $payrollPeriod->locked_at !== null) {
            throw new \Exception("Payroll is already locked", 403);
        }

        return ['payroll_period_id' => $payrollPeriod->id];
    }

    public function handleUpdate(int $id, array $data, string $mode): EmployeeDeduction
    {
        $payrollPeriod = $this->checkPayrollPeriodLock();

        switch ($mode) {
            case 'complete':
                return $this->complete($id);

            case 'stop':
                return $this->stop($id);

            default:
                $dto = array_merge($data, $payrollPeriod);
                return $this->update($id, $dto);
        }
    }
}