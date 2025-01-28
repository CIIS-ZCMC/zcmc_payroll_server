<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'leave_types';

    protected $primaryKey = 'id';


    protected $casts = [
        'is_special' => 'boolean',
        'is_active' => 'boolean',
        'is_country' => 'boolean',
        'is_illness' => 'boolean',
        'is_study' => 'boolean',
        'is_days_recommended' => 'boolean'
    ];
}
