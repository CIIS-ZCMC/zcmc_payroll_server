<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receivable extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'receivables';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
        'type',
        'condition_operator',
        'condition_value',
        'percent_value',
        'fixed_amount',
        'billing_cycle',
        'status',
    ];

    public $timestamps = true;
}
