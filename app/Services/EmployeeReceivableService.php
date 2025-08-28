<?php

namespace App\Services;

use App\Contract\EmployeeReceivableInterface;

class EmployeeReceivableService
{
    public function __construct(private EmployeeReceivableInterface $interface)
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

    public function delete($id)
    {
        return $this->interface->delete($id);
    }

    public function complete($id)
    {
        return $this->interface->complete($id);
    }

    public function stop($id)
    {
        return $this->interface->stop($id);
    }
}
