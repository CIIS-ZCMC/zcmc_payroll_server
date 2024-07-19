<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    use HasFactory;

    protected $table = 'receivables';

    protected $primaryKey = 'id';

    protected $fillable = [
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

}
