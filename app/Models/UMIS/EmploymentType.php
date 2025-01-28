<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentType extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'employment_types';

    protected $primaryKey = 'id';

    public function employeeProfiles()
    {
        return $this->hasMany(EmployeeProfile::class);
    }
}
