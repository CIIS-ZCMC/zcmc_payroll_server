<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTax extends Model
{
    use HasFactory;

    protected $table = 'employee_taxes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_list_id',
        'with_holding_tax',
        'month',
        'year',
    ];

    public $timestamps = true;

    public function EmployeeList()
    {
        return $this->belongsTo(EmployeeList::class);
    }
}
