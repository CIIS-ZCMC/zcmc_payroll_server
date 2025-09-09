<?php

namespace App\Contract\Repositories;

use App\Contract\EmployeeInterface;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EmployeeRepository implements EmployeeInterface
{
    public function __construct(private Employee $model, private EmployeeTimeRecordRepository $repository)
    {
        //nothinng
    }

    public function index(Request $request): Collection
    {
        $payroll_period_id = $request->input('payroll_period_id');
        $status = $request->input(' status');
        return $this->repository->index($payroll_period_id, $status);
    }


    public function create(array $data): Employee
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Employee
    {
        $model = $this->model->find($id);
        $model->update($data);
        return $model->fresh();
    }
}