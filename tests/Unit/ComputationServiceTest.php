<?php

namespace Tests\Unit;

use App\Services\ComputationService;
use PHPUnit\Framework\TestCase;

/**
 * ComputationService is pure arithmetic with no database access, so these are
 * plain unit tests — no application boot, no migrations.
 */
class ComputationServiceTest extends TestCase
{
    private ComputationService $compute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compute = new ComputationService;
    }

    // ---------------------------------------------------------------- hazard

    /**
     * The 2016 rules table, checked at every boundary rather than in the middle
     * of each band — the bands are where an off-by-one would hide.
     *
     * @dataProvider salaryGradePercentages
     */
    public function test_hazard_percentage_by_salary_grade(int $grade, float $percentage)
    {
        $this->assertSame(
            round(100000 * $percentage, 2),
            round($this->compute->hazardAmount('Permanent Full-time', $grade, 100000), 2),
            "Salary grade {$grade} should attract {$percentage} of basic salary."
        );
    }

    public static function salaryGradePercentages(): array
    {
        return [
            'grade 1 (bottom of the <=19 band)' => [1, 0.25],
            'grade 19 (top of the <=19 band)' => [19, 0.25],
            'grade 20' => [20, 0.15],
            'grade 21' => [21, 0.13],
            'grade 22' => [22, 0.12],
            'grade 23' => [23, 0.11],
            'grade 24' => [24, 0.10],
            'grade 25' => [25, 0.10],
            'grade 26' => [26, 0.09],
            'grade 27' => [27, 0.08],
            'grade 28' => [28, 0.07],
            'grade 29' => [29, 0.06],
            'grade 30' => [30, 0.06],
            'grade 31 (top of the table)' => [31, 0.05],
            'grade 32 (off the top of the table)' => [32, 0.00],
        ];
    }

    public function test_part_time_hazard_is_halved()
    {
        $fullTime = $this->compute->hazardAmount('Permanent Full-time', 15, 100000);
        $partTime = $this->compute->hazardAmount('Permanent Part-time', 15, 100000);

        $this->assertSame($fullTime / 2, $partTime);
    }

    public function test_job_order_employees_get_no_hazard_pay()
    {
        $this->assertSame(0.00, $this->compute->hazardAmount('Job Order', 15, 100000));
    }

    /**
     * Eligibility is lost at 11 absences, not 12 — the boundary the old
     * duplicate implementations disagreed about.
     */
    public function test_hazard_is_lost_at_eleven_absences()
    {
        $this->assertGreaterThan(0, $this->compute->hazardAmount('Permanent Full-time', 15, 100000, 10));
        $this->assertSame(0.00, $this->compute->hazardAmount('Permanent Full-time', 15, 100000, 11));
        $this->assertSame(0.00, $this->compute->hazardAmount('Permanent Full-time', 15, 100000, 12));
    }

    public function test_hazard_is_lost_at_eleven_leave_days()
    {
        $this->assertGreaterThan(0, $this->compute->hazardAmount('Permanent Full-time', 15, 100000, 0, 10));
        $this->assertSame(0.00, $this->compute->hazardAmount('Permanent Full-time', 15, 100000, 0, 11));
    }

    public function test_absences_below_the_cutoff_do_not_reduce_hazard_pay()
    {
        $this->assertSame(
            $this->compute->hazardAmount('Permanent Full-time', 15, 100000, 0),
            $this->compute->hazardAmount('Permanent Full-time', 15, 100000, 5)
        );
    }

    // ------------------------------------------------------------------ PERA

    public function test_full_time_pera_is_the_full_fixed_amount()
    {
        $this->assertSame(2000.00, $this->compute->peraAmount($this->pera(2000), 22, 'Permanent Full-time', 0));
    }

    public function test_part_time_pera_is_halved()
    {
        $this->assertSame(1000.00, $this->compute->peraAmount($this->pera(2000), 22, 'Permanent Part-time', 0));
    }

    public function test_job_order_employees_get_no_pera()
    {
        $this->assertSame(0.00, $this->compute->peraAmount($this->pera(2000), 22, 'Job Order', 0));
    }

    /**
     * One day's PERA is the monthly amount over the required duty days, rounded
     * to the centavo before it is multiplied by the absence count.
     */
    public function test_pera_is_prorated_against_absences()
    {
        // 2000 / 22 = 90.91 a day; three absences costs 272.73.
        $this->assertSame(
            2000.00 - 272.73,
            $this->compute->peraAmount($this->pera(2000), 19, 'Permanent Full-time', 3)
        );
    }

    public function test_part_time_pera_is_prorated_at_the_half_rate()
    {
        // 1000 / 22 = 45.45 a day; two absences costs 90.90.
        $this->assertSame(
            1000.00 - 90.90,
            $this->compute->peraAmount($this->pera(2000), 20, 'Permanent Part-time', 2)
        );
    }

    /**
     * An employee who was present for a day or less draws no PERA at all —
     * the guard that keeps a near-empty month from producing a full allowance.
     */
    public function test_one_present_day_or_fewer_earns_no_pera()
    {
        $this->assertSame(0.00, $this->compute->peraAmount($this->pera(2000), 1, 'Permanent Full-time', 0));
        $this->assertSame(0.00, $this->compute->peraAmount($this->pera(2000), 0, 'Permanent Full-time', 0));
        $this->assertGreaterThan(0, $this->compute->peraAmount($this->pera(2000), 2, 'Permanent Full-time', 0));
    }

    public function test_the_duty_day_divisor_is_overridable()
    {
        // 2000 / 20 = 100.00 a day.
        $this->assertSame(
            1900.00,
            $this->compute->peraAmount($this->pera(2000), 19, 'Permanent Full-time', 1, 20)
        );
    }

    /** A stand-in for the Receivable row the caller loads once and passes in. */
    private function pera(float $fixedAmount): object
    {
        return (object) ['fixed_amount' => $fixedAmount];
    }
}
