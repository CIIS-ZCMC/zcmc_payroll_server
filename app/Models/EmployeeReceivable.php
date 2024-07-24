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
        'date_from',
        'date_to',
        'is_default'
    ];

    public $timestamps = true;

    public function EmployeeList()
    {
        return $this->belongsTo(EmployeeList::class);
    }

    public function Receivable()
    {
        return $this->belongsTo(Receivable::class);
    }
}
