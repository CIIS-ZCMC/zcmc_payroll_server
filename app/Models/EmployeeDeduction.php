<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EmployeeDeduction extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $table = 'employee_deductions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'deduction_id',
        'billing_cycle',
        'amount',
        'percentage',
        'date_from',
        'date_to',
        'with_terms',
        'total_term',
        'total_paid',
        'reason',
        'status',
        'isDifferential',
        'is_default',
        'effective_date',
        'deduct_at',
        'stopped_at',
        'completed_at',
    ];
    public $timestamps = true;

    //Spatie
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('employee-deduction')
            ->logFillable()
            ->logOnlyDirty();
    }

    //Version 2
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function deductions()
    {
        return $this->belongsTo(Deduction::class, 'deduction_id');
    }
}
