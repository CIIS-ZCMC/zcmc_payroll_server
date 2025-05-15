<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcludedEmployee extends Model
{
    use HasFactory;

    protected $table = 'excluded_employees';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'month',
        'year',
        'period_start',
        'period_end',
        'reason',
        'is_removed'
    ];

    public $timestamps = true;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

}
