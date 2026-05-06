<?php

namespace App\Observers;

use App\Models\EmployeeReceivable;
use App\Models\EmployeeReceivableLog;

class EmployeeReceivableObserver
{
    /**
     * Handle the EmployeeReceivable "created" event.
     *
     * @param  \App\Models\EmployeeReceivable  $employeeReceivable
     * @return void
     */
    public function created(EmployeeReceivable $employeeReceivable)
    {
        EmployeeReceivableLog::create([
            'employee_receivable_id' => $employeeReceivable->id,
            'action_by' => auth()->user()->id,
            'action' => 'created'
        ]);
    }

    /**
     * Handle the EmployeeReceivable "updated" event.
     *
     * @param  \App\Models\EmployeeReceivable  $employeeReceivable
     * @return void
     */
    public function updated(EmployeeReceivable $employeeReceivable)
    {
          $remarks = [];

        if ($employeeReceivable->isDirty('status')) {
            $remarks[] = "Status changed to {$employeeReceivable->status}";
        }

        if ($employeeReceivable->isDirty('amount')) {
            $remarks[] = "Amount changed to {$employeeReceivable->amount}";
        }

        if ($employeeReceivable->isDirty("billing_cycle")) {
            $remarks[] = "Billing cycle changed to {$employeeReceivable->billing_cycle}";
        }

        if ($employeeReceivable->isDirty("total_paid")) {
            $remarks[] = "Total paid changed to {$employeeReceivable->total_paid}";
        }
        
        EmployeeReceivableLog::create([
            'employee_receivable_id' => $employeeReceivable->id,
            'action_by' => auth()->user()->id,
            'action' => 'updated',
            'remarks' => $remarks,
            'details' => $employeeReceivable->getDirty()
        ]);
    }

    /**
     * Handle the EmployeeReceivable "deleted" event.
     *
     * @param  \App\Models\EmployeeReceivable  $employeeReceivable
     * @return void
     */
    public function deleted(EmployeeReceivable $employeeReceivable)
    {
        EmployeeReceivableLog::create([
            'employee_receivable_id' => $employeeReceivable->id,
            'action_by' => auth()->user()->id,
            'action' => 'deleted'
        ]);
    }

    /**
     * Handle the EmployeeReceivable "restored" event.
     *
     * @param  \App\Models\EmployeeReceivable  $employeeReceivable
     * @return void
     */
    public function restored(EmployeeReceivable $employeeReceivable)
    {
        EmployeeReceivableLog::create([
            'employee_receivable_id' => $employeeReceivable->id,
            'action_by' => auth()->user()->id,
            'action' => 'restored'
        ]);
    }

    /**
     * Handle the EmployeeReceivable "force deleted" event.
     *
     * @param  \App\Models\EmployeeReceivable  $employeeReceivable
     * @return void
     */
    public function forceDeleted(EmployeeReceivable $employeeReceivable)
    {
        EmployeeReceivableLog::create([
            'employee_receivable_id' => $employeeReceivable->id,
            'action_by' => auth()->user()->id,
            'action' => 'force_deleted'
        ]);
    }
}
