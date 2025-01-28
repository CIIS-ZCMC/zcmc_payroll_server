<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InActiveEmployee extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'in_active_employees';

    protected $primaryKey = 'id';


    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class);
    }
}
