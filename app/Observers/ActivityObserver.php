<?php

namespace App\Observers;

use App\Models\Activity;

/**
 * Enriches every activity-log entry with request context that Spatie
 * does not capture out of the box (client IP and user agent).
 */
class ActivityObserver
{
    public function creating(Activity $activity): void
    {
        $properties = $activity->properties ?? collect();

        $activity->properties = $properties->merge([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
