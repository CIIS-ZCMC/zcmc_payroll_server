<?php

namespace App\Services;

use App\Contract\EmployeeReceivableInterface;
use App\Data\EmployeeReceivableData;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class EmployeeReceivableService
{
    public function __construct(private EmployeeReceivableInterface $interface)
    {
        //Nothing
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
        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        if ($mode === 'paginate') {
            return $this->paginate($perPage, $page);
        }

        return $this->getAll();
    }

    public function create(EmployeeReceivableData $data): EmployeeReceivable
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
            'receivable_id' => $data->receivable_id,
            'frequency' => $data->frequency,
            'amount' => $amount,
            'percentage' => $percentage,
            'date_from' => $data->date_from,
            'date_to' => $data->date_to,
            'total_paid' => $data->total_paid,
            'reason' => $data->reason,
            'status' => $data->status,
            'is_default' => $data->is_default,
            'stopped_at' => $data->stopped_at,
            'completed_at' => $data->completed_at,
        ]);
    }

    public function upsert(array $data): int
    {
        $records = array_map(fn(EmployeeReceivableData $data) => $data->toArray(), $data);
        return $this->interface->upsert($records);
    }

    public function store(array $dtos)
    {
        if (count($dtos) === 1) {
            return $this->create($dtos[0]);
        }

        return $this->upsert($dtos);
    }

    public function update(int $id, array $data): EmployeeReceivable
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
            'is_default' => $data['is_default'],
            'reason' => $data['reason'],
        ]);
    }

    public function delete($id): bool
    {
        return $this->interface->delete($id);
    }

    public function complete($id): EmployeeReceivable
    {
        return $this->interface->complete($id);
    }

    public function stop($id): EmployeeReceivable
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

    public function handleUpdate(int $id, array $data, string $mode): EmployeeReceivable
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
