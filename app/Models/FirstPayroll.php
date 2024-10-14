<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirstPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'general_payrolls_id',
        'employee_list_id',
        'net_total_salary',
        'locked_at',
    ];
}
