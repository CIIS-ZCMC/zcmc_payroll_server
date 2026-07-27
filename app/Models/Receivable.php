<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receivable extends Model
{
    use LogsModelActivity;

    /** Stable code for the PERA system receivable. */
    public const CODE_PERA = 'PERA';

    /** Stable code for the Hazard Pay system receivable. */
    public const CODE_HAZARD = 'HAZARD';

    protected $fillable = [
        'receivable_group_id',
        'name',
        'code',
        'fixed_amount',
        'is_active',
    ];

    protected $casts = [
        'fixed_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ReceivableGroup::class, 'receivable_group_id');
    }

    public function employeeReceivables(): HasMany
    {
        return $this->hasMany(EmployeeReceivable::class);
    }
}
