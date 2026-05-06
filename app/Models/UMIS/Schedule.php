<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UMIS\TimeShift;

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

    public function timeShift()
    {
        return $this->belongsTo(TimeShift::class, 'time_shift_id');
    }
}
