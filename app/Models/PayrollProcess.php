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
        'is_dirty',
        'recomputed_at',
        'started_by',
        'started_at',
    ];

    /**
     * A run that has never been recomputed is dirty. The column carries the
     * same default, but Eloquent does not read database defaults back, so
     * without this the model returned by create() — and therefore the create
     * response — would report is_dirty false on a run that has computed
     * nothing.
     */
    protected $attributes = [
        'is_dirty' => true,
    ];

    protected $casts = [
        'current_step' => 'integer',
        'payroll_type' => 'integer',
        'is_dirty' => 'boolean',
        'recomputed_at' => 'datetime',
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
