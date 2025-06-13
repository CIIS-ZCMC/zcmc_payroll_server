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

    public function employeeSalary()
    {
        // return $this->hasMany(EmployeeSalary::class)->latest();
        return $this->hasOne(EmployeeSalary::class)->latestOfMany();
    }

    public function employeeTimeRecords()
    {
        return $this->hasMany(EmployeeTimeRecord::class);
    }

    public function employeeComputedSalaries()
    {
        return $this->hasMany(EmployeeComputedSalary::class);
    }

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    public function employeeReceivables()
    {
        return $this->hasMany(EmployeeReceivable::class);
    }
}
