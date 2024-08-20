<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoppageLog extends Model
{
    use HasFactory;

    protected $table = 'stoppage_logs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_deduction_id',
        'status',
        'date_to',
        'date_from',
        'reason'
    ];
}
