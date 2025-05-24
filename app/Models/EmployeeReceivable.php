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

    public function logs()
    {
        return $this->hasMany(EmployeeDeductionLog::class);
    }

    public function employeeList()
    {
        return $this->belongsTo(EmployeeList::class, 'employee_list_id');
    }

    public function receivables()
    {
        return $this->belongsTo(Receivable::class, 'receivable_id');
    }

    public function getReceivable()
    {
        return $this->belongsTo(Receivable::class, 'id');
    }

    public function receivableLogs()
    {
        return $this->hasMany(EmployeeReceivableLog::class);
    }

    public function stoppageLogs()
    {
        return $this->hasMany(StoppageLog::class, 'employee_receivable_id');
    }
}
