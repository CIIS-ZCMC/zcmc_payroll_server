<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcludedEmployee extends Model
{
    use HasFactory;

    protected $table = 'excluded_employees';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_list_id',
        'payroll_headers_id',
        'reason',
        'year',
        'month',
        'is_removed'
    ];

    public $timestamps = true;

    public function employeeList()
    {
        return $this->belongsTo(EmployeeList::class);
    }


}
