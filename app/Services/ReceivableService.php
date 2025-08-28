<?php

namespace App\Services;

use App\Contract\ReceivableInterface;

class ReceivableService
{
    public function __construct(private ReceivableInterface $interface)
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
}
