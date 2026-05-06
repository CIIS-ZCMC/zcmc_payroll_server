<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivableRule extends Model
{
    use HasFactory;
    
    protected $table = 'receivable_rules';
    
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'receivable_id',
        'min_salary',
        'max_salary',
        'apply_type',
        'value',
        'effective_date'
    ];
    
    public $timestamps = true;
    
    public function receivables()
    {
        return $this->belongsTo(Receivable::class);
    }
}
