<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivableLog extends Model
{
    use HasFactory;

    protected $table = 'receivable_logs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'receivable_id',
        'action_by',
        'action'
    ];

    public $timestamps = true;

    public function receivables()
    {
        return $this->belongsTo(Receivable::class);
    }
}
