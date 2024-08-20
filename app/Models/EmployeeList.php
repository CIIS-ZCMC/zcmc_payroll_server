<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeReceivable;

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
    public function getEmployeeReceivables(){
        return $this->hasMany(EmployeeReceivable::class,'employee_list_id');
    }
    public function getSalary(){
        return $this->hasOne(EmployeeSalary::class,'employee_list_id')->latest();
    }

    public function getSalaries(){
        return $this->hasMany(EmployeeSalary::class,'employee_list_id');
    }

    public function getTaxes(){
        return $this->hasMany(EmployeeTax::class,'employee_list_id');
    }
    public function getTimeRecords(){
        return $this->hasOne(TimeRecord::class,'employee_list_id')
                ->where('is_active', 1);
    }

    public function isPayrollExcluded(){
        $timeRecord = $this->getTimeRecords;

        if (!$timeRecord) {
            return $this->hasMany(ExcludedEmployee::class)->whereRaw('1 = 0');
        }
        return $this->hasMany(ExcludedEmployee::class, 'employee_list_id')
            ->where('month', $timeRecord->month)
            ->where('year', $timeRecord->year);
    }

    public function getListOfTimeRecords(){
        return $this->hasMany(TimeRecord::class,'employee_list_id');
    }
    public function getListOfDeductions() {
        return $this->hasMany(EmployeeDeduction::class,'employee_list_id');
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
