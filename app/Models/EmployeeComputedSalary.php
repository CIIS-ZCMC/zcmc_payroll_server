<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeComputedSalary extends Model
{
    use HasFactory;

    protected $table = 'employee_computed_salaries';

    protected $primaryKey = 'id';

    protected $fillable = [
        'time_record_id',
        'computed_salary'
    ];

    public function timeRecord()
    {
        return $this->belongsTo(TimeRecord::class);
    }

    public $timestamps = true;
}
