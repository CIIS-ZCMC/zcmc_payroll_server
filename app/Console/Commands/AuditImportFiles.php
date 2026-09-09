<?php

namespace App\Console\Commands;

use App\Models\Deduction;
use App\Models\Receivable;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Reports whether a folder of import files will actually import.
 *
 * The decision on code drift was to standardise the source files rather than
 * teach the importer every spelling the payroll office has used. This is the
 * tool for doing that: point it at a month's folder and it says, per file,
 * whether B1 resolves against the catalog and what the nearest codes are when
 * it does not — plus whether the layout is the expected one.
 *
 *   php artisan payroll:audit-import-files "C:/.../08_AUGUST DEDUCTION PAYROLL"
 */
class AuditImportFiles extends Command
{
    protected $signature = 'payroll:audit-import-files
                            {path : Folder containing the .xlsx/.csv import files}
                            {--kind=auto : deduction, receivable, or auto (R-prefixed files are receivables)}';

    protected $description = 'Check a folder of payroll import files against the deduction/receivable catalog';

    public function handle(): int
    {
        $path = rtrim($this->argument('path'), '/\\');

        if (! is_dir($path)) {
            $this->error("Not a folder: {$path}");

            return self::FAILURE;
        }

        $deductionCodes = Deduction::pluck('code')->filter()->all();
        $receivableCodes = Receivable::pluck('code')->filter()->all();

        if ($deductionCodes === [] && $receivableCodes === []) {
            $this->error('The deduction and receivable catalogs are empty. Seed them first.');

            return self::FAILURE;
        }

        $files = collect(array_merge(
            glob($path . '/*.xlsx') ?: [],
            glob($path . '/*.xls') ?: [],
            glob($path . '/*.csv') ?: []
        ))->sort()->values();

        if ($files->isEmpty()) {
            $this->warn("No .xlsx/.xls/.csv files in {$path}");

            return self::SUCCESS;
        }

        $rows = [];
        $problems = 0;

        foreach ($files as $file) {
            $name = basename($file);
            $kind = $this->kindFor($name);
            $catalog = $kind === 'receivable' ? $receivableCodes : $deductionCodes;

            try {
                $sheet = Excel::toArray(new \stdClass, $file)[0] ?? [];
            } catch (\Throwable $e) {
                $rows[] = [$name, $kind, '—', 'UNREADABLE', $e->getMessage()];
                $problems++;
                continue;
            }

            $code = trim((string) ($sheet[0][1] ?? ''));

            if ($code === '') {
                $rows[] = [$name, $kind, '(empty)', 'NO CODE', 'Cell B1 is empty.'];
                $problems++;
                continue;
            }

            $layout = $this->layoutNote($sheet);

            if (in_array($code, $catalog, true)) {
                $rows[] = [$name, $kind, $code, 'OK', $layout];
                continue;
            }

            $rows[] = [$name, $kind, $code, 'UNKNOWN', trim('Similar: ' . $this->nearest($code, $catalog) . '. ' . $layout)];
            $problems++;
        }

        $this->table(['File', 'Kind', 'B1 code', 'Status', 'Note'], $rows);

        $this->newLine();
        $this->line(sprintf(
            '%d file(s) checked, %d need attention.',
            $files->count(),
            $problems
        ));

        if ($problems > 0) {
            $this->line('Fix cell B1 to the exact catalog code, or regenerate the file with:');
            $this->line('  php artisan payroll:import-template <CODE> --out=<folder>');
            $this->newLine();
            $this->warn('"Similar" is closest-spelling only and can be wrong — COOP1 is CML,');
            $this->warn('not COOPL1. docs/IMPORT_TEMPLATE.md §5 has the verified mapping.');
        }

        return $problems > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function kindFor(string $filename): string
    {
        $kind = $this->option('kind');

        if ($kind !== 'auto') {
            return $kind;
        }

        // The office names receivable files R01-, R02-, and deductions D01-.
        return preg_match('/^R\d/i', $filename) === 1 ? 'receivable' : 'deduction';
    }

    /**
     * @param  array<int, array>  $sheet
     */
    private function layoutNote(array $sheet): string
    {
        $header = strtolower(trim((string) ($sheet[2][1] ?? '')));

        if (strpos($header, 'employee') === false) {
            return 'Row 3 is not the column header row — data must start on row 4.';
        }

        return '';
    }

    /**
     * @param  array<int, string>  $catalog
     */
    private function nearest(string $code, array $catalog): string
    {
        $normalise = fn (string $value) => strtoupper(preg_replace('/[^A-Z0-9]/i', '', $value));
        $target = $normalise($code);

        $scored = [];

        foreach ($catalog as $candidate) {
            $scored[$candidate] = levenshtein($target, $normalise($candidate));
        }

        asort($scored);

        return implode(', ', array_slice(array_keys($scored), 0, 3));
    }
}
