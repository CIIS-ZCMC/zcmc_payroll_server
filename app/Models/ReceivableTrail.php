<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivableTrail extends Model
{
    use HasFactory;
    protected $table = 'receivable_trails';

    protected $primaryKey = 'id';

    protected $fillable = [
        'receivable_id',
        'status',
        'from',
        'to',
        'reason'
    ];

    public $timestamps = true;

    public function receivables()
    {
        return $this->belongsTo(Receivable::class);
    }
}
