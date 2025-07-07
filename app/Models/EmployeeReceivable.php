<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class EmployeeReceivable extends Model
{
    use HasFactory;

    protected $table = 'employee_receivables';

    protected $primaryKey = 'id';

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'receivable_id',
        'amount',
        'percentage',
        'is_default',
        'status',
        'date_to',
        'date_from',
        'stopped_at',
        'completed_at',
        'reason',
        'total_paid',
        'frequency',

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
