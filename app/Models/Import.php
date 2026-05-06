<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Import extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'imports';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_id',
        'receivables_id',
        'file_name',
        'employment_type',
        'payroll_date'
    ];

    public $timestamps = true;
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('import')
            ->logFillable()
            ->logOnlyDirty();
    }
}
