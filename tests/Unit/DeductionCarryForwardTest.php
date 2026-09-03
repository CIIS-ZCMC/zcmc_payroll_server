<?php

namespace Tests\Unit;

use App\Models\EmployeeDeduction;
use App\Support\DeductionCarryForward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Support\PayrollFixture;
use Tests\TestCase;

class DeductionCarryForwardTest extends TestCase
{
    use RefreshDatabase;

    private PayrollFixture $fixture;

    private DeductionCarryForward $carry;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-01-10 08:00:00'));

        $this->fixture = (new PayrollFixture)->build();
        $this->carry = app(DeductionCarryForward::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_an_active_deduction_carries_forward()
    {
        $this->carry->carry($this->fixture->periods['jan_first']);

        $this->assertDatabaseHas('employee_deductions', [
            'payroll_period_id' => $this->fixture->periods['jan_first']->id,
            'employee_id' => $this->fixture->employees['full_time_clean']->id,
            'deduction_id' => $this->fixture->deductions['wtax']->id,
        ]);
    }

    public function test_a_stopped_deduction_does_not_carry_forward()
    {
        $this->assertNotCarried('part_time', 'coop');
    }

    public function test_a_completed_deduction_does_not_carry_forward()
    {
        $this->assertNotCarried('part_time_absent', 'coop');
    }

    public function test_a_finished_term_does_not_carry_forward()
    {
        // total_paid 12 of total_term 12.
        $this->assertNotCarried('full_time_absent', 'gsis_cl');
    }

    public function test_an_expired_deduction_does_not_carry_forward()
    {
        // date_to 2025-12-31, and the clock is set to 2026-01-10.
        $this->assertNotCarried('on_leave', 'pagibig_ml');
    }

    public function test_a_mid_term_deduction_carries_forward_without_advancing()
    {
        $january = $this->fixture->periods['jan_first'];

        $this->carry->carry($january);

        $this->assertSame(5, $this->totalPaid($january->id, 'full_time_clean', 'gsis_cl'));
    }

    public function test_carrying_twice_writes_nothing_the_second_time()
    {
        $january = $this->fixture->periods['jan_first'];

        $written = $this->carry->carry($january);
        $this->assertGreaterThan(0, $written);

        $after = $this->rows($january->id);

        $this->assertSame(0, $this->carry->carry($january));
        $this->assertEquals($after, $this->rows($january->id));
    }

    public function test_advancing_terms_only_touches_term_based_deductions()
    {
        $january = $this->fixture->periods['jan_first'];
        $this->carry->carry($january);

        $wtaxBefore = $this->totalPaid($january->id, 'full_time_clean', 'wtax');

        $this->carry->advanceTerms($january);

        $this->assertSame(6, $this->totalPaid($january->id, 'full_time_clean', 'gsis_cl'));
        $this->assertSame(
            $wtaxBefore,
            $this->totalPaid($january->id, 'full_time_clean', 'wtax'),
            'A deduction without terms should not have been advanced.'
        );
    }

    public function test_advancing_terms_stops_at_the_final_instalment()
    {
        $january = $this->fixture->periods['jan_first'];
        $this->carry->carry($january);

        // 5 of 12 -> advance seven times to reach the last instalment.
        for ($i = 0; $i < 8; $i++) {
            $this->carry->advanceTerms($january);
        }

        $this->assertSame(12, $this->totalPaid($january->id, 'full_time_clean', 'gsis_cl'));
    }

    public function test_a_period_with_no_predecessor_carries_nothing()
    {
        $this->assertSame(0, $this->carry->carry($this->fixture->periods['dec_second']));
    }

    public function test_it_can_be_limited_to_selected_employees()
    {
        $january = $this->fixture->periods['jan_first'];
        $only = $this->fixture->employees['full_time_clean'];

        $this->carry->carry($january, [$only->id]);

        $employeeIds = EmployeeDeduction::where('payroll_period_id', $january->id)
            ->pluck('employee_id')
            ->unique()
            ->all();

        $this->assertSame([$only->id], $employeeIds);
    }

    private function assertNotCarried(string $employeeKey, string $deductionKey): void
    {
        $january = $this->fixture->periods['jan_first'];

        $this->carry->carry($january);

        $this->assertDatabaseMissing('employee_deductions', [
            'payroll_period_id' => $january->id,
            'employee_id' => $this->fixture->employees[$employeeKey]->id,
            'deduction_id' => $this->fixture->deductions[$deductionKey]->id,
        ]);
    }

    private function totalPaid(int $periodId, string $employeeKey, string $deductionKey): int
    {
        return (int) DB::table('employee_deductions')
            ->where('payroll_period_id', $periodId)
            ->where('employee_id', $this->fixture->employees[$employeeKey]->id)
            ->where('deduction_id', $this->fixture->deductions[$deductionKey]->id)
            ->value('total_paid');
    }

    private function rows(int $periodId): array
    {
        return DB::table('employee_deductions')
            ->where('payroll_period_id', $periodId)
            ->orderBy('employee_id')
            ->orderBy('deduction_id')
            ->get(['employee_id', 'deduction_id', 'amount', 'status', 'total_paid'])
            ->map(fn ($r) => (array) $r)
            ->all();
    }
}
