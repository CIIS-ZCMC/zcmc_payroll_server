<?php

namespace App\Models;

use App\Models\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReceivableGroup extends Model
{
    use LogsModelActivity;

    protected $fillable = ['name', 'code'];

    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class);
    }
}
