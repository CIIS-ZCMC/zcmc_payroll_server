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
        'employee_profile_id',
        'name',
    ];

    public $timestamps = true;
}
