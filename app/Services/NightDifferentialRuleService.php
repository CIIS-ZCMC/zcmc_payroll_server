<?php

namespace App\Services;

use App\Contract\NightDifferentialRuleInterface;
use App\Data\NightDifferentialRuleData;

class NightDifferentialRuleService
{
    public function __construct(private NightDifferentialRuleInterface $interface)
    {
        //Nothing
    }

    public function getAll()
    {
        return $this->interface->getAll();
    }

    public function show(int $id)
    {
        return $this->interface->show($id);
    }

    public function updateOrCreate(NightDifferentialRuleData $data)
    {
        return $this->interface->updateOrCreate($data->toArray());
    }

    public function findByEmploymentType(string $employment_type)
    {
        return $this->interface->findByEmploymentType($employment_type);
    }

}
