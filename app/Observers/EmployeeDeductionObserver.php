<?php

namespace App\Observers;

use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionLog;

class EmployeeDeductionObserver
{
    /**
     * Handle the EmployeeDeduction "created" event.
     *
     * @param  \App\Models\EmployeeDeduction  $employeeDeduction
     * @return void
     */
    public function created(EmployeeDeduction $employeeDeduction)
    {
        EmployeeDeductionLog::create([
            'employee_deduction_id' => $employeeDeduction->id,
            'action_by' => auth()->user()->id,
            'action' => 'created'
        ]);
    }

    /**
     * Handle the EmployeeDeduction "updated" event.
     *
     * @param  \App\Models\EmployeeDeduction  $employeeDeduction
     * @return void
     */
    public function updated(EmployeeDeduction $employeeDeduction)
    {
        $remarks = [];

        if ($employeeDeduction->isDirty('status')) {
            $remarks[] = "Status changed to {$employeeDeduction->status}";
        }

        if ($employeeDeduction->isDirty('amount')) {
            $remarks[] = "Amount changed to {$employeeDeduction->amount}";
        }

        if ($employeeDeduction->isDirty('remarks')) {
            $remarks[] = "Remarks changed to {$employeeDeduction->remarks}";
        }

        if ($employeeDeduction->isDirty("billing_cycle")) {
            $remarks[] = "Billing cycle changed to {$employeeDeduction->billing_cycle}";
        }

        if ($employeeDeduction->isDirty("total_term")) {
            $remarks[] = "Total term changed to {$employeeDeduction->total_term}";
        }

        if ($employeeDeduction->isDirty("total_paid")) {
            $remarks[] = "Total paid changed to {$employeeDeduction->total_paid}";
        }
        
        EmployeeDeductionLog::create([
            'employee_deduction_id' => $employeeDeduction->id,
            'action_by' => auth()->user()->id,
            'action' => 'updated',
            'remarks' => $remarks,
            'details' => $employeeDeduction->getDirty()
        ]);
    }

    /**
     * Handle the EmployeeDeduction "deleted" event.
     *
     * @param  \App\Models\EmployeeDeduction  $employeeDeduction
     * @return void
     */
    public function deleted(EmployeeDeduction $employeeDeduction)
    {
        EmployeeDeductionLog::create([
            'employee_deduction_id' => $employeeDeduction->id,
            'action_by' => auth()->user()->id,
            'action' => 'deleted'
        ]);
    }

    /**
     * Handle the EmployeeDeduction "restored" event.
     *
     * @param  \App\Models\EmployeeDeduction  $employeeDeduction
     * @return void
     */
    public function restored(EmployeeDeduction $employeeDeduction)
    {
        EmployeeDeductionLog::create([
            'employee_deduction_id' => $employeeDeduction->id,
            'action_by' => auth()->user()->id,
            'action' => 'restored'
        ]);
    }

    /**
     * Handle the EmployeeDeduction "force deleted" event.
     *
     * @param  \App\Models\EmployeeDeduction  $employeeDeduction
     * @return void
     */
    public function forceDeleted(EmployeeDeduction $employeeDeduction)
    {
        EmployeeDeductionLog::create([
            'employee_deduction_id' => $employeeDeduction->id,
            'action_by' => auth()->user()->id,
            'action' => 'force_deleted'
        ]);
    }
}
