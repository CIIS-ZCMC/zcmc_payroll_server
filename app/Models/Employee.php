<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'employee_profile_id',
        'employee_number',
        'first_name',
        'last_name',
        'middle_name',
        'extension_name',
        'designation',
        'hire_date',
        'is_newly_hired',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_newly_hired' => 'boolean',
    ];

    public function salaries(): HasMany
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function timeRecords(): HasMany
    {
        return $this->hasMany(EmployeeTimeRecord::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    public function receivables(): HasMany
    {
        return $this->hasMany(EmployeeReceivable::class);
    }

    public function nightDuties(): HasMany
    {
        return $this->hasMany(EmployeeNightDuty::class);
    }

    public function exclusions(): HasMany
    {
        return $this->hasMany(EmployeeExclusion::class);
    }

    public function computedSalaries(): HasMany
    {
        return $this->hasMany(EmployeeComputedSalary::class);
    }
}
