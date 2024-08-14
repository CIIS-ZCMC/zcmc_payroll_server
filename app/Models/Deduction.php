<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    protected $table = 'deductions';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_group_id',
        'name',
        'code',
        'amount',
        'percentage',
        'date_from',
        'date_to',
        'emmployment_type',
        'is_mandatory',
        'is_active'
    ];

    public $timestamps = true;

    public function DeductionGroup()
    {
        return $this->belongsTo(DeductionGroup::class);
    }

    public function Logs()
    {
        return $this->belongsTo(DeductionLog::class,'id');
    }

}
