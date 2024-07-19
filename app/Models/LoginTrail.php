<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginTrail extends Model
{
    use HasFactory;

    protected $table = 'login_trails';

    protected $primaryKey = 'id';

    protected $fillable = [
        'action_by',
        'module_name',
        'methods',
        'description',
        'status'
    ];

    public $timestamps = true;
}
