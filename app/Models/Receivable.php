<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    use HasFactory;

    protected $table = 'receivables';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
        'employment_type',
        'charge_basis',
        'charge_value',
        'billing_cycle',
        'terms_to_pay',
        'is_applied_to_all',
        'apply_salarygrade_from',
        'apply_salarygrade_to',
        'is_mandatory',
        'status',
        'reason',
        'stopped_at'
    ];

    public $timestamps = true;

    public function employeeList()
    {
        return $this->belongsToMany(EmployeeList::class, 'employee_receivables');
    }

    public function employeeReceivables()
    {
        return $this->hasMany(EmployeeReceivable::class, 'receivable_id');
    }

    public function logs()
    {
        return $this->hasMany(ReceivableLog::class);
    }
}
