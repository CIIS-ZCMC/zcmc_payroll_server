<?php

namespace Tests\Feature;

use App\Contract\PayrollReportInterface;
use App\Http\Resources\PayrollReportResource;
use App\Services\EmployeePreviewService;
use App\Services\PayrollReportService;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

/**
 * Pins the observable output of the payroll pipeline before it is refactored.
 *
 * These snapshots record what the code does today, correct or not. Where a
 * snapshot captures behavior we already believe to be wrong, the test that
 * follows it says so explicitly, so the diff in a later phase reads as the
 * intended fix rather than as a regression.
 *
 * Regenerate with: UPDATE_SNAPSHOTS=1 vendor/bin/phpunit --filter PayrollGoldenMaster
 */
class PayrollGoldenMasterTest extends TestCase
{
    use RefreshDatabase;

    private PayrollFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        // Snapshots carry created_at/updated_at, so the clock has to stand
        // still or every run diffs against itself.
        Carbon::setTestNow(Carbon::parse('2026-01-10 08:00:00'));

        $this->fixture = (new PayrollFixture)->build();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_preview_output_is_unchanged()
    {
        $period = $this->fixture->periods['jan_first'];
        $service = app(EmployeePreviewService::class);

        foreach (['included', 'excluded', 'all'] as $type) {
            $result = $service->getAll($type, $period->id, []);

            $this->assertMatchesSnapshot(
                "preview_{$type}",
                json_decode(json_encode($result['data']), true)
            );
        }
    }

    public function test_payroll_report_resource_is_unchanged()
    {
        $period = $this->fixture->periods['jan_first'];

        $query = app(PayrollReportInterface::class)->getEmployeePayrollReport($period->id);
        $data = PayrollReportResource::make($query)->resolve();

        $this->assertMatchesSnapshot(
            'payroll_report_resource',
            json_decode(json_encode($data), true)
        );
    }

    public function test_exported_workbook_cells_are_unchanged()
    {
        $period = $this->fixture->periods['jan_first'];

        $response = app(PayrollReportService::class)->exportEmployeePayrollReport($period->id);

        ob_start();
        $response->sendContent();
        $binary = ob_get_clean();

        $path = tempnam(sys_get_temp_dir(), 'payroll') . '.xlsx';
        file_put_contents($path, $binary);

        try {
            $spreadsheet = IOFactory::load($path);

            $cells = [];

            foreach ($spreadsheet->getAllSheets() as $sheet) {
                // Values only. Styles are re-emitted by PhpSpreadsheet in ways
                // that churn between versions and would make the snapshot
                // brittle without telling us anything about the payroll rules.
                $cells[$sheet->getTitle()] = $sheet->toArray(null, true, false, false);
            }

            $this->assertMatchesSnapshot('export_cells', $cells);
        } finally {
            @unlink($path);
        }
    }

    /**
     * The preview is a read. It must not write deductions, advance a term, or
     * otherwise change the database — that work belongs to the sync and the
     * posting path now (see DeductionCarryForwardTest for those rules).
     */
    public function test_previewing_writes_nothing()
    {
        $period = $this->fixture->periods['jan_first'];
        $service = app(EmployeePreviewService::class);

        $before = $this->allDeductionRows();

        $service->getAll('all', $period->id, []);
        $service->getAll('included', $period->id, []);
        $service->preview('all', $period->id, [], 10, 1);

        $this->assertEquals($before, $this->allDeductionRows());
    }

    /** @return array<int, array<string, mixed>> */
    private function allDeductionRows(): array
    {
        return DB::table('employee_deductions')
            ->orderBy('id')
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();
    }

    /**
     * Records how many queries a full-period preview costs today, so the
     * Phase 3 work has a number to beat rather than an impression.
     */
    public function test_preview_query_count_is_recorded()
    {
        $period = $this->fixture->periods['jan_first'];

        DB::enableQueryLog();
        app(EmployeePreviewService::class)->getAll('all', $period->id, []);
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        $employees = count($this->fixture->employees);

        $this->assertMatchesSnapshot('preview_query_count', [
            'employees' => $employees,
            'queries' => $queries,
        ]);
    }

    private function assertMatchesSnapshot(string $name, $actual): void
    {
        $path = __DIR__ . "/../fixtures/{$name}.json";
        $encoded = json_encode($actual, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

        if (getenv('UPDATE_SNAPSHOTS') || ! file_exists($path)) {
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            file_put_contents($path, $encoded);

            $this->addWarning("Snapshot written: {$name}");

            return;
        }

        $this->assertSame(
            file_get_contents($path),
            $encoded,
            "Snapshot '{$name}' changed."
        );
    }
}
