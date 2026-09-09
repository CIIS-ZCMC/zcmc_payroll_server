<?php

namespace App\Console\Commands;

use App\Exports\ImportTemplateExport;
use App\Models\Deduction;
use App\Models\Receivable;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Emits an import template in the canonical layout with a verified catalog code
 * already in cell B1.
 *
 * The code drift the sample files show — WTAX vs TAX, GSIS CONSO vs GCONSO,
 * HDMF Premium vs PAGIBIG-PREMIUM — comes from B1 being typed by hand each
 * month. A generated template removes the opportunity.
 *
 *   php artisan payroll:import-template TAX --month=February --year=2026
 */
class MakeImportTemplate extends Command
{
    protected $signature = 'payroll:import-template
                            {code : The exact deduction or receivable code for cell B1}
                            {--month= : Month name written into row 2, e.g. February}
                            {--year= : Year written into row 2}
                            {--out=storage/app/import-templates : Folder to write the file into}
                            {--format=xlsx : xlsx or csv}';

    protected $description = 'Generate a blank payroll import template for one deduction or receivable code';

    public function handle(): int
    {
        $code = $this->argument('code');

        $deduction = Deduction::where('code', $code)->first();
        $receivable = $deduction ? null : Receivable::where('code', $code)->first();

        if (! $deduction && ! $receivable) {
            $this->error("No deduction or receivable has the code \"{$code}\".");
            $this->line('Run `php artisan payroll:audit-import-files <folder>` to see the codes in use,');
            $this->line('or check the deductions/receivables catalog for the exact spelling.');

            return self::FAILURE;
        }

        $kind = $deduction ? 'deduction' : 'receivable';
        $name = $deduction->name ?? $receivable->name;

        $month = $this->option('month');
        $year = $this->option('year');

        if (($month === null) !== ($year === null)) {
            $this->error('Give both --month and --year, or neither. Row 2 is all-or-nothing.');

            return self::FAILURE;
        }

        $format = strtolower($this->option('format'));

        if (! in_array($format, ['xlsx', 'csv'], true)) {
            $this->error('--format must be xlsx or csv.');

            return self::FAILURE;
        }

        $directory = rtrim($this->option('out'), '/\\');

        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            $this->error("Could not create {$directory}");

            return self::FAILURE;
        }

        // Codes contain spaces and punctuation (e.g. "GSIS L & R", "PPREM.").
        $safeCode = preg_replace('/[^A-Za-z0-9]+/', '-', $code);
        $file = $directory . '/' . trim($safeCode, '-') . '.' . $format;

        Excel::store(
            new ImportTemplateExport($code, $month, $year === null ? null : (int) $year),
            $file,
            null,
            $format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX
        );

        // Excel::store() writes relative to the configured disk root.
        $this->info(sprintf('Template for %s "%s" (%s) written to %s', $kind, $name, $code, $file));

        return self::SUCCESS;
    }
}
