<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'designations';

    protected $primaryKey = 'id';

    public function plantilla()
    {
        return $this->hasMany(Plantilla::class);
    }

    public function assignAreas()
    {
        return $this->hasMany(AssignArea::class);
    }
}
