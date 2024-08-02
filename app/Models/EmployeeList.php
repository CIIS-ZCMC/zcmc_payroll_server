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
        'total_term_paid',
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

    public function deductions()
    {
        return $this->belongsToMany(Deduction::class, 'employee_deductions')
            ->using(EmployeeDeduction::class)
            ->withPivot('amount', 'percentage', 'frequency', 'total_term', 'is_default')
            ->withTimestamps();
    }

    public function receivables()
    {
        return $this->belongsToMany(Receivable::class, 'employee_receivables')
            ->using(EmployeeReceivable::class)
            ->withPivot('amount', 'percentage', 'total_term', 'date_from', 'date_to', 'is_default')
            ->withTimestamps();
    }

    public function taxes()
    {
        return $this->hasMany(EmployeeTax::class);
    }

    public function timeRecords()
    {
        return $this->hasMany(TimeRecord::class);
    }
}
