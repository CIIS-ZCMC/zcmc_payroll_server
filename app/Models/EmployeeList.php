<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeList extends Model
{
    use HasFactory;

    protected $table = 'employee_lists';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_profile_id',
        'employee_number',
        'total_term_paid',
        'first_name',
        'last_name',
        'middle_name',
        'ext_name',
        'designation',
        'status',
        'is_newly_hired'
    ];

    public $timestamps = true;
}
