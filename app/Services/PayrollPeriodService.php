<?php

namespace App\Services;

use App\Contract\PayrollPeriodInterface;
use App\Models\PayrollPeriod;

class PayrollPeriodService
{
    public function __construct(private PayrollPeriodInterface $interface)
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

    public function lock($id)
    {
        return $this->interface->lock($id);
    }

    public function find($id)
    {
        return PayrollPeriod::find($id);
    }
}
