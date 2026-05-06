<?php

namespace App\Services;

use App\Contract\EmployeeReceivableTrailInterface;

class EmployeeReceivableTrailService
{
    public function __construct(private EmployeeReceivableTrailInterface $interface)
    {
        //Nothing
    }

    public function create(array $data)
    {
        return $this->interface->create($data);
    }

    public function show(int $employee_id, int $receivable_id)
    {
        return $this->interface->show($employee_id, $receivable_id);
    }
}
