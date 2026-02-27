<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionLog extends Model
{
    use HasFactory;

    protected $table = 'deduction_logs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_id',
        'deduction_group_id',
        'action_by',
        'action'
    ];

    public $timestamps = true;

    public function Deduction()
    {
        return $this->belongsTo(Deduction::class);
    }

    public function DeductionGroup()
    {
        return $this->belongsTo(DeductionGroup::class);
    }
}
