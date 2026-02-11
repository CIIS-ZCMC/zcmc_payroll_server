<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeReceivable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employee_receivables';

    protected $primaryKey = 'id';

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'receivable_id',
        'billing_cycle',
        'amount',
        'percentage',
        'date_from',
        'date_to',
        'total_paid',
        'reason',
        'status',
        'is_default',
        'effective_date',
        'received_at',
        'stopped_at',
        'completed_at',
    ];

    public $timestamps = true;

    //Version 2
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function receivables()
    {
        return $this->belongsTo(Receivable::class, 'receivable_id');
    }
}
