<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * One employee's include/exclude decision for one payroll run.
 *
 * Who decided, and why, is worth keeping: this is the record of a person
 * choosing to pay or not pay somebody, so it carries an activity log like the
 * other financial tables.
 */
class PayrollSelection extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'payroll_selections';

    protected $primaryKey = 'id';

    protected $fillable = [
        'payroll_period_id',
        'payroll_type',
        'employee_id',
        'is_selected',
        'reason',
        'selected_by',
    ];

    protected $casts = [
        'payroll_type' => 'integer',
        'is_selected' => 'boolean',
    ];

    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('payroll-selection')
            ->logFillable()
            ->logOnlyDirty();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function scopeForRun($query, int $payrollPeriodId, int $payrollType)
    {
        return $query->where('payroll_period_id', $payrollPeriodId)
            ->where('payroll_type', $payrollType);
    }
}
