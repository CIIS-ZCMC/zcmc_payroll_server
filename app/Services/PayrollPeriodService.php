<?php

namespace App\Services;

use App\Models\PayrollPeriod;

class PayrollPeriodService
{
    public function index()
    {
        return PayrollPeriod::all();
    }

    public function create(array $data)
    {
        return PayrollPeriod::create($data);
    }

    public function update($id, array $data)
    {
        $model = PayrollPeriod::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        $model = PayrollPeriod::find($id);
        if ($model) {
            $model->delete();
            return true;
        }
        return false;
    }

    public function find($id)
    {
        return PayrollPeriod::find($id);
    }
}
