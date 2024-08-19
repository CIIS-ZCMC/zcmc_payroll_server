<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionGroup extends Model
{
    use HasFactory;

    protected $table = 'deduction_groups';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code'
    ];

    public $timestamps = true;
<<<<<<< HEAD
    
    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }
=======

>>>>>>> c5375f205dd4c99c8b8c7cbb17e65b13d8a24823
}
