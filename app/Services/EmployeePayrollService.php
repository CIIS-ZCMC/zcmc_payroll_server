<?php

namespace App\Services;

use App\Contract\EmployeePayrollInterface;
use App\Models\EmployeePayroll;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use App\Services\GuardService;

class EmployeePayrollService
{
    public function __construct(
        private EmployeePayrollInterface $interface,
        private GuardService $guard
    ) {
        //Nothing
    }

    public function paginate(int $perPage, int $page)
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function create(array $data)
    {
        $this->guard->ensureNotLocked((int) $data['payroll_period_id']);
        return $this->interface->create($data);
    }

    public function update($id, array $data)
    {
        $periodId = $data['payroll_period_id']
            ?? EmployeePayroll::where('id', $id)->value('payroll_period_id');

        $this->guard->ensurePeriodNotLocked($periodId === null ? null : (int) $periodId);

        return $this->interface->update($id, $data);
    }

    /**
     * A batch can legitimately span more than one period, so every distinct
     * period in it is checked — not just whichever one happened to be active.
     */
    public function updateOrInsert(array $data): int
    {
        foreach (array_unique(array_column($data, 'payroll_period_id')) as $periodId) {
            $this->guard->ensureNotLocked((int) $periodId);
        }

        return $this->interface->upsert($data);
    }
}