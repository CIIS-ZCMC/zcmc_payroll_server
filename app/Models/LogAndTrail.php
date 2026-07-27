<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAndTrail extends Model
{
    protected $table = 'logs_and_trails';

    protected $fillable = [
        'action_by_id',
        'action_by_name',
        'module',
        'action_type',
        'reference_table',
        'reference_id',
        'changes',
        'description',
        'ip_address',
        'status',
    ];

    protected $casts = ['changes' => 'json'];
}
