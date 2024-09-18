<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionTrail extends Model
{
    use HasFactory;

    protected $table = 'deduction_trails';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_id',
        'status',
        'from',
        'to',
        'reason'
    ];

    public $timestamps = true;

    public function deductions()
    {
        return $this->belongsTo(Deduction::class);
    }
}
