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

    public function EmployeeReceivableLogs()
    {
        return $this->belongsTo(EmployeeReceivable::class);
    }
}
