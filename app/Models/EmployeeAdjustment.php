<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAdjustment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'employee_adjustments';

    protected $primaryKey = 'id';

    protected $fillable = [
        'action_by',
        'payroll_period_id',
        'employee_deduction_id',
        'employee_receivable_id',
        'amount',
        'amount_to_pay',
        'amount_balance',
        'reason'
    ];

    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('employee-adjustment')
            ->logFillable()
            ->logOnlyDirty();
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employeeDeduction()
    {
        return $this->belongsTo(EmployeeDeduction::class, 'employee_deduction_id');
    }

    public function employeeReceivable()
    {
        return $this->belongsTo(EmployeeReceivable::class, 'employee_receivable_id');
    }

}
