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
        $this->guard->ensureNotLocked();
        return $this->interface->create($data);
    }

    public function update($id, array $data)
    {
        $this->guard->ensureNotLocked();
        return $this->interface->update($id, $data);
    }
    
    public function updateOrInsert(array $data): int
    {
        $this->guard->ensureNotLocked();
        return $this->interface->upsert($data);
    }
}