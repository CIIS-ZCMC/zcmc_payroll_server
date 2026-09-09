<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Provenance for a bulk deduction/receivable upload.
 *
 * The fillable list below used to name four columns the table does not have
 * (`receivables_id`, `file_path`, and — until the accompanying migration —
 * `file_size`, `file_type`, `imported_at`) while omitting the two it does
 * (`receivable_id`, `path`). The names here now match the schema.
 */
class ImportFiles extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'import_files';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deduction_id',
        'receivable_id',
        'file_name',
        'path',
        'file_size',
        'file_type',
        'imported_at',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
    ];

    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('import')
            ->logFillable()
            ->logOnlyDirty();
    }

    public function deduction()
    {
        return $this->belongsTo(Deduction::class, 'deduction_id');
    }

    public function receivable()
    {
        return $this->belongsTo(Receivable::class, 'receivable_id');
    }
}
