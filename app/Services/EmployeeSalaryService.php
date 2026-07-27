<?php

namespace App\Services;

use App\Contract\EmployeeSalaryInterface;
use App\Models\EmployeeSalary;

class EmployeeSalaryService
{
    public function __construct(private EmployeeSalaryInterface $salaries) {}

    public function save(array $data): EmployeeSalary
    {
        return $this->salaries->updateOrCreate($data);
    }

    public function update(int $id, array $data): EmployeeSalary
    {
        return $this->salaries->update($id, $data);
    }

    /**
     * Bulk import of salaries keyed by (employee_id, payroll_period_id).
     */
    public function import(array $rows): int
    {
        return $this->salaries->upsert($rows);
    }
}
