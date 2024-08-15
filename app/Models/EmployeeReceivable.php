<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReceivable extends Model
{
    use HasFactory;

    protected $table = 'employee_receivables';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_list_id',
        'receivable_id',
        'amount',
        'percentage',
        'total_term',
        'is_default',
        'status',
        'date_to',
        'date_from',
        'stopped_at'
    ];

    public $timestamps = true;

    public function logs()
    {
        return $this->hasMany(EmployeeDeductionLog::class);
    }

<<<<<<< HEAD
    public function employeeList()
    {
        return $this->belongsTo(EmployeeList::class, 'employee_list_id');
    }

    public function receivables()
    {
        return $this->belongsTo(Receivable::class, 'receivable_id');
=======
    public function getReceivable()
    {
        return $this->belongsTo(Receivable::class,'id');
    }

    public function receivableLogs(){
        return $this->hasMany(EmployeeReceivableLog::class);
>>>>>>> c5375f205dd4c99c8b8c7cbb17e65b13d8a24823
    }
}
