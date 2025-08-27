<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeShift extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; 
    protected $table = 'time_shifts';
    protected $primaryKey = 'id';
    
}
