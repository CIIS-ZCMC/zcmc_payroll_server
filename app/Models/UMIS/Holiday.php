<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'holidays';

    protected $primaryKey = 'id';
}
