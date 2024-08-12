<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeList extends Model
{
    use HasFactory;

    protected $table = 'employee_lists';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_profile_id',
        'employee_number',
        'first_name',
        'last_name',
        'middle_name',
        'ext_name',
        'designation',
        'status',
        'is_newly_hired'
    ];

    public $timestamps = true;


    public function salary()
    {
        return $this->hasOne(EmployeeSalary::class);
    }

    public function taxes()
    {
        return $this->hasMany(EmployeeTax::class);
    }

    public function timeRecords()
    {
        return $this->hasMany(TimeRecord::class);
    }

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class, 'employee_list_id');
    }

    public function employeeReceivables()
    {
        return $this->hasMany(EmployeeReceivable::class, 'employee_list_id');
    }
}
