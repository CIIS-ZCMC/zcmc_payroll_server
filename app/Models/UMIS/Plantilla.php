<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'plantilla';

    protected $primaryKey = 'id';

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function assignedAreas()
    {
        return $this->hasMany(AssignArea::class);
    }
}
