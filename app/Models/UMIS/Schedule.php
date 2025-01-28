<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'schedules';

    protected $primaryKey = 'id';

    public function employeeProfile()
    {
        return $this->belongsToMany(EmployeeProfile::class, 'employee_profile_schedule')->withPivot('id', 'employee_profile_id');
    }
}
