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
        'reason'
    ];

    public $timestamps = true;

    public function EmployeeList()
    {
        return $this->belongsTo(EmployeeList::class);
    }


}
