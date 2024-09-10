<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoppageLog extends Model
{
    use HasFactory;

    protected $table = 'stoppage_logs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_deduction_id',
        'employee_receivable_id',
        'status',
        'date_to',
        'date_from',
        'reason',
        'stopped_at',
    ];

    public function employeeDeduction()
    {
        return $this->belongsTo(EmployeeDeduction::class, 'employee_deduction_id');
    }

    public function employeeReceivable()
    {
        return $this->belongsTo(EmployeeReceivable::class, 'employee_receivable_id');
    }
}
