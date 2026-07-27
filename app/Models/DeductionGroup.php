<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeductionGroup extends Model
{
    use LogsModelActivity;

    protected $fillable = ['name', 'code'];

    public function deductions(): HasMany
    {
        return $this->hasMany(Deduction::class);
    }
}
