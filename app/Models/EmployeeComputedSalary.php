<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeComputedSalary extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'employee_computed_salaries';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'employee_time_record_id',
        'computed_salary'
    ];

    public $timestamps = true;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function employeeTimeRecord()
    {
        return $this->belongsTo(EmployeeTimeRecord::class);
    }
}
