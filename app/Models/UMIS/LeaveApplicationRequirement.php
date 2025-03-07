<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplicationRequirement extends Model
{
    use HasFactory;
    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'leave_application_requirements';

    public $fillable = [
        'leave_application_id',
        'name',
        'file_name',
        'path',
        'size'
    ];

    public function leave_application()
    {
        return $this->belongsTo(LeaveApplication::class);
    }

}
