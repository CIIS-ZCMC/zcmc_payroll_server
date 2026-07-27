<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

/**
 * Application activity model, extending Spatie's so we can attach an
 * observer that enriches every logged activity with request context.
 */
#[ObservedBy([ActivityObserver::class])]
class Activity extends SpatieActivity
{
    //
}
