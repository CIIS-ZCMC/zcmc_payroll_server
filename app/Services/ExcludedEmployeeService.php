<?php

namespace App\Services;

use App\Contract\ExcludedEmployeeInterface;
use App\Data\ExcludedEmployeeData;
use App\Models\ExcludedEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ExcludedEmployeeService
{
    public function __construct(private ExcludedEmployeeInterface $interface)
    {
        //nothing
    }

    public function getAll(int $payroll_period_id): Collection
    {
        return $this->interface->getAll($payroll_period_id);
    }

    public function paginate(int $perPage, int $page, int $payroll_period_id): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page, $payroll_period_id);
    }

    public function index(Request $request): Collection|LengthAwarePaginator
    {
        $mode = $request->mode;
        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        if ($mode === 'paginate') {
            return $this->paginate($perPage, $page, $request->payroll_period_id);
        }

        return $this->getAll($request->payroll_period_id);
    }

    public function create(ExcludedEmployeeData $data): ExcludedEmployee
    {
        return $this->interface->create([
            'payroll_period_id' => $data->payroll_period_id,
            'employee_id' => $data->employee_id,
            'month' => $data->month,
            'year' => $data->year,
            'period_start' => $data->period_start,
            'period_end' => $data->period_end,
            'reason' => $data->reason,
            'is_removed' => $data->is_removed,
        ]);
    }

    public function update(int $id, array $data): ExcludedEmployee
    {
        return $this->interface->update($id, [
            'reason' => $data['reason'],
            'is_removed' => $data['is_removed'],
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }

}