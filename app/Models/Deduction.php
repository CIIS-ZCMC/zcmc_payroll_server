<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deduction extends Model
{
    use LogsModelActivity;

    protected $fillable = [
        'deduction_group_id',
        'name',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(DeductionGroup::class, 'deduction_group_id');
    }

    public function employeeDeductions(): HasMany
    {
        return $this->hasMany(EmployeeDeduction::class);
    }
}
