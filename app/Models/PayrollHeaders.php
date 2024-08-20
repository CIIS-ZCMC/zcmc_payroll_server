<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralPayroll;

class PayrollHeaders extends Model
{
    use HasFactory;
    protected $table = "payroll_headers";
    protected $fillable = [
        'month',
        'year',
        'created_by',
        'is_locked',
    ];


    public function genPayrolls(){
        return $this->hasMany(GeneralPayroll::class,'payroll_headers_id');
    }
}
