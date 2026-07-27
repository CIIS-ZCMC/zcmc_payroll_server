<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Shared activity-log configuration for payroll domain models.
 *
 * Records create/update/delete events, capturing only the fillable
 * attributes that actually changed. Logs are grouped by table name
 * so activity can be filtered per module.
 */
trait LogsModelActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getTable());
    }
}
