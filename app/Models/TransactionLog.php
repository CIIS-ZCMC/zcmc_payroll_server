<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;

    protected $table = 'transaction_logs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'module',
        'action',
        'status',
        'ip_address',
        'remarks',
        'serverResponse',
        'affected_entity',
        'employee_profile_id',
        'employee_number',
        'name',
    ];

    public $timestamps = true;
}
