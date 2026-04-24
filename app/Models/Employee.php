<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_profile_id',
        'employee_number',
        'first_name',
        'last_name',
        'middle_name',
        'extension_name',
        'designation',
        'assigned_area',
        'is_newly_hired',
        'is_excluded',
        'is_resigned',
        'status',
    ];
    public $timestamps = true;

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        $middleInitial = $this->middle_name
            ? strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : '';

        return "{$this->last_name}, {$this->first_name} {$middleInitial}";
    }

    public function employeeSalary()
    {
        return $this->hasOne(EmployeeSalary::class);
    }

    public function employeeTimeRecords()
    {
        return $this->hasOne(EmployeeTimeRecord::class);
    }

    public function employeeComputedSalary()
    {
        return $this->hasOne(EmployeeComputedSalary::class);
    }

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    public function employeeReceivables()
    {
        return $this->hasMany(EmployeeReceivable::class);
    }

    public function excludedEmployees()
    {
        return $this->belongsTo(ExcludedEmployee::class);
    }

    public function employeePayroll()
    {
        return $this->hasMany(EmployeePayroll::class);
    }
}
