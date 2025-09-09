<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReceivableTrail extends Model
{
    use HasFactory;
    protected $table = 'employee_receivable_trails';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_receivable_id',
        'total_term',
        'total_term_paid',
        'amount_paid',
        'date_paid',
        'balance',
        'status',
        'remarks',
        'is_last_payment',
        'is_adjustment'
    ];

    public $timestamps = true;

    public function employeeReceivable()
    {
        return $this->belongsTo(EmployeeReceivable::class);
    }
}
