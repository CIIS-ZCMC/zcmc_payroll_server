<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    protected $table = 'imports';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_id',
        'receivables_id',
        'file_name',
        'employment_type',
        'payroll_date'
    ];

    public $timestamps = true;
}
