<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReceivableLog extends Model
{
    use HasFactory;

    protected $table = 'employee_receivable_logs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_receivable_id',
        'action_by',
        'action'
    ];

    public $timestamps = true;

<<<<<<< HEAD
    public function employeeReceivable()
=======
    public function EmployeeReceivableLogs()
>>>>>>> c5375f205dd4c99c8b8c7cbb17e65b13d8a24823
    {
        return $this->belongsTo(EmployeeReceivable::class);
    }
}
