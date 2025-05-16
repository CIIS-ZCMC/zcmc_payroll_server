<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $table = 'employee_salaries';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'employment_type',
        'base_salary',
        'salary_grade',
        'salary_step',
        'month',
        'year',
        'is_active'
    ];

    public $timestamps = true;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
