<?php

namespace Tests\Unit;

use App\Models\PayrollPeriod;
use App\Support\PayrollPeriodResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollPeriodResolverTest extends TestCase
{
    use RefreshDatabase;

    private PayrollPeriodResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new PayrollPeriodResolver;
    }

    public function test_second_half_looks_back_to_the_same_months_first_half()
    {
        $first = $this->period(3, 2026, 'permanent', 'first_half');
        $second = $this->period(3, 2026, 'permanent', 'second_half');

        $this->assertSame($first->id, $this->resolver->previousPeriod($second)->id);
    }

    public function test_first_half_looks_back_to_the_previous_months_second_half()
    {
        $february = $this->period(2, 2026, 'permanent', 'second_half');
        $march = $this->period(3, 2026, 'permanent', 'first_half');

        $this->assertSame($february->id, $this->resolver->previousPeriod($march)->id);
    }

    public function test_january_rolls_back_into_the_previous_year()
    {
        $december = $this->period(12, 2025, 'permanent', 'second_half');
        $january = $this->period(1, 2026, 'permanent', 'first_half');

        $this->assertSame($december->id, $this->resolver->previousPeriod($january)->id);
    }

    /**
     * The variant this class replaced ordered by id and ignored employment_type,
     * so a permanent period could inherit from a job-order one.
     */
    public function test_employment_types_do_not_borrow_from_each_other()
    {
        $this->period(12, 2025, 'job_order', 'second_half');
        $permanentDecember = $this->period(12, 2025, 'permanent', 'second_half');
        $january = $this->period(1, 2026, 'permanent', 'first_half');

        $this->assertSame($permanentDecember->id, $this->resolver->previousPeriod($january)->id);
    }

    public function test_job_order_resolves_within_its_own_employment_type()
    {
        $jobOrderDecember = $this->period(12, 2025, 'job_order', 'second_half');
        $this->period(12, 2025, 'permanent', 'second_half');
        $january = $this->period(1, 2026, 'job_order', 'first_half');

        $this->assertSame($jobOrderDecember->id, $this->resolver->previousPeriod($january)->id);
    }

    public function test_the_earliest_period_has_no_predecessor()
    {
        $first = $this->period(1, 2026, 'permanent', 'first_half');

        $this->assertNull($this->resolver->previousPeriod($first));
    }

    public function test_a_gap_in_the_calendar_is_not_skipped_over()
    {
        // October exists, November does not. December must not silently reach
        // back to October.
        $this->period(10, 2025, 'permanent', 'second_half');
        $december = $this->period(12, 2025, 'permanent', 'first_half');

        $this->assertNull($this->resolver->previousPeriod($december));
    }

    private function period(int $month, int $year, string $employmentType, string $periodType): PayrollPeriod
    {
        return PayrollPeriod::create([
            'month' => (string) $month,
            'year' => (string) $year,
            'employment_type' => $employmentType,
            'period_type' => $periodType,
            'period_start' => $periodType === 'first_half' ? 1 : 16,
            'period_end' => $periodType === 'first_half' ? 15 : 31,
        ]);
    }
}
