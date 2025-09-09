<?php

namespace App\Services;

use App\Contract\GeneralPayrollInterface;

class GeneralPayrollService
{
    public function __construct(private GeneralPayrollInterface $interface)
    {
        //Nothing
    }

    public function create(array $data)
    {
        return $this->interface->create($data);
    }

    public function update($id, array $data)
    {
        return $this->interface->update($id, $data);
    }
}
