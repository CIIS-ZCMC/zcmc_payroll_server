<?php

namespace App\Services;

use App\Models\ExcludedEmployee;


class ExcludeEmployeeService
{
    public function create(array $data)
    {
        return ExcludedEmployee::create($data);
    }

    public function update($id, array $data)
    {
        $excludedEmployee = ExcludedEmployee::find($id);
        if ($excludedEmployee) {
            $excludedEmployee->update($data);
            return $excludedEmployee;
        }
        return null;
    }
}