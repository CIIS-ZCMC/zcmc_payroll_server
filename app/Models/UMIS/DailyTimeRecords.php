<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTimeRecords extends Model
{
    use HasFactory;
    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'daily_time_records';

    protected $primaryKey = 'id';

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class, 'biometric_id', 'biometric_id');
    }
}
