<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PayrollProcess extends Model
{
    use HasFactory, LogsActivity;

    protected $table = "payroll_processes";

    protected $primaryKey = 'id';

    protected $fillable = [
        'payroll_period_id',
        'payroll_type',
        'current_step',
        'status',
        'started_by',
        'started_at',
    ];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('payroll_process')
            ->logFillable()
            ->logOnlyDirty();
    }
    
    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

}
